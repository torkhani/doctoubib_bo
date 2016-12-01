<?php

namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

use Doctoubib\ModelsBundle\Entity\Doctor;

class WebScrapingCommand extends ContainerAwareCommand
{

    protected $client;
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('admin:web_scraping_command')
            ->setDescription('Crawl website');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->client = new Client(array('base_uri' => 'http://www.med.tn'));
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $categories = [
            'cardiologue',
            'dentiste',
            'dermatologue',
            'generaliste',
            'gynecologue-obstetricien',
            'ophtalmologiste',
            'oto-rhino-laryngologiste-(orl)',
            'pediatre',
            'psychiatre',
            'allergologue',
            'anatomo-cyto-pathologiste',
            'anesthesiste-reanimateur',
            'angiologue',
            'biochimiste',
            'biochimiste-clinique',
            'biologiste-medicale',
            'biophysique',
            'cancerologue',
            'chirurgien',
            'chirurgien-cancerologue',
            'chirurgien-cardio-vasculaire',
            'chirurgien-cardio-vasculaire-thoracique',
            'chirurgien-esthetique',
            'chirurgien-generaliste',
            'chirurgien-maxillo-facial-stomatologue',
            'chirurgien-orthopediste',
            'chirurgien-orthopediste-traumatologue',
            'chirurgien-pediatrique',
            'chirurgien-plasticien',
            'chirurgien-thoracique',
            'chirurgien-urologue',
            'diabetologue',
            'dieteticien',
            'embryologiste',
            'endocrinologue',
            'endocrinologue-diabetologue',
            'gastro-enterologue',
            'geriatre',
            'gynecologue',
            'hematologue',
            'hematologue-clinique',
            'hematopathologiste',
            'imagerie-medicale',
            'immunologiste',
            'immunopathologiste',
            'interniste',
            'interniste-maladies-infectieuses',
            'interniste-reanimation-medicale'
        ];

        foreach ($categories as $category) {

            $listDoctorsByCategory = $this->getDomFromUrl('medecin/'.$category);
            $crawler = new Crawler($listDoctorsByCategory->getContents());
            $filter = $crawler->filter('li.doc_result_list');

            if (iterator_count($filter) > 1) {
                foreach ($filter as $i => $content) {
                    $crawler = new Crawler($content);
                    $doctorPageLink = $crawler->filter('.praticien__name a')->attr('href');

                    $doctorPageContent = $this->getDomFromUrl($doctorPageLink);

                    $doctorCrawler = new Crawler($doctorPageContent->getContents());

                    $doctor = new Doctor();

                    if ($doctorCrawler->filter('.pf-itempage-maindiv .docinfo h1')->count()) {
                        $name = $doctorCrawler->filter('.pf-itempage-maindiv .docinfo h1')->text();
                        $doctor->setFirstname($name);
                        $doctor->setLastname($name);
                        $doctor->setCivility('M');
                    }

                    if ($doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=telephone]')->count()) {
                        $telephone = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=telephone]')->text();
                        $doctor->setPhoneNumber($telephone);
                    }

                    if ($doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=email]')->count()) {
                        $email = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=email]')->text();
                    } else {
                        $email = 'no@doctoubib.com';
                    }

                    $doctor->setEmail($email);

                    if ($doctorCrawler->filter('span[itemprop="streetAddress"]')->count()) {
                        $address = $doctorCrawler->filter('span[itemprop="streetAddress"]')->text();
                        $doctor->setAdress($address);
                    }

                    if ($doctorCrawler->filter('span[itemprop="addressRegion"]')->count()) {
                        $regionName = $doctorCrawler->filter('span[itemprop="addressRegion"]')->text();
                        $region = $em->getRepository('DoctoubibModelsBundle:Region')->findOneByName($regionName);
                        $doctor->setRegion($region);
                    }

                    if ($doctorCrawler->filter('span[itemprop="addressLocality"]')->count()) {
                        $cityName = $doctorCrawler->filter('span[itemprop="addressLocality"]')->text();
                        $city = $em->getRepository('DoctoubibModelsBundle:City')->findOneByName($cityName);
                        $doctor->setCity($city);
                    }

                    if ($doctorCrawler->filter('meta[itemprop="latitude"]')->count()) {
                        $doctorInfo['latitude'] = $doctorCrawler->filter('meta[itemprop="latitude"]')->text();
                    }

                    if ($doctorCrawler->filter('meta[itemprop="longitude"]')->count()) {
                        $doctorInfo['longitude'] = $doctorCrawler->filter('meta[itemprop="longitude"]')->text();
                    }

                    if ($doctorCrawler->filter('.pfdetailitem-subelement.pf-onlyitem > p')->count()) {
                        $descriptionHtml = $doctorCrawler->filter('.pfdetailitem-subelement.pf-onlyitem > p')->html();
                        $description = explode('<br>', $descriptionHtml);

                        $description = serialize($description);
                        $doctor->setDescription($description);
                    }


                    $speciality = $em->getRepository('DoctoubibModelsBundle:Speciality')->find(3);

                    $doctor->setInsurance(true);
                    $doctor->setConsultationPriceMin(35);
                    $doctor->addSpeciality($speciality);

                    $em->persist($doctor);
                    $em->flush();

                }
            } else {
                throw new RuntimeException('Got empty result processing the dataset!');
            }
        }
    }

    private function getDomFromUrl($url)
    {
        // create a request
        $request = $this->client->get($url);
        $result = $request->getBody();

        return $result;
    }
}
