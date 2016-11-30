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

                    $doctorInfo['name'] = $doctorCrawler->filter('.pf-itempage-maindiv .docinfo h1')->text();
                    if ($telephone = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=telephone]')->count()) {
                        $doctorInfo['telephone'] = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=telephone]')->text();
                    }

                    if ($email = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=email]')->count()) {
                        $doctorInfo['email'] = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=email]')->text();
                    }
                    if($doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=address]')->count()) {
                        $address = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=address]')->html();
                        print_r($address);
                        $region = substr(strrchr($address, "<br>"), 4);
                        var_dump($region);die();
                        $doctorInfo['address'] = $stuff;
                    }

                    if($doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=address]')->count()) {
                        $doctorInfo['address'] = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=address]')->html();
                    }

                    if($doctorCrawler->filter('.pfdetailitem-subelement.pf-onlyitem > p')->count()) {
                        $doctorInfo['description'] = $doctorCrawler->filter('.pfdetailitem-subelement.pf-onlyitem > p')->html();
                    }

                    $em = $this->getContainer()->get('doctrine.orm.entity_manager');

                    $doctor = new Doctor();
                    $speciality = $em->getRepository('DoctoubibModelsBundle:Speciality')->find(1);
                    $region = $em->getRepository('DoctoubibModelsBundle:Region')->find(1);
                    $doctor->setFirstname($doctorInfo['name']);
                    $doctor->setLastname($doctorInfo['name']);
                    $doctor->setCivility('M');
                    $doctor->setInsurance(true);
                    $doctor->setConsultationPriceMin(35);
                    $doctor->setEmail($doctorInfo['email']);
                    $doctor->setAdress($doctorInfo['address']);
                    $doctor->setRegion($region);
                    $doctor->setPhoneNumber($doctorInfo['telephone']);
                    $doctor->setSpeciality($speciality);

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
