<?php

namespace AdminBundle\Command;

use Doctoubib\ModelsBundle\Entity\Speciality;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Doctoubib\ModelsBundle\Entity\Doctor;
use Symfony\Component\Console\Exception\RuntimeException;

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
            'cardiologue' => 'Cardiologue',
            'dentiste' => 'Chirugien-dentiste',
            'dermatologue' => 'Dermatologue',
            'generaliste' => 'Médecin généraliste',
            'gynecologue-obstetricien' => 'Gynécologue',
            'ophtalmologiste' => 'Ophtalmologue',
            'oto-rhino-laryngologiste-(orl)' => 'ORL',
            'oto-rhino-laryngologiste-orl' => 'ORL',
            'maladies-infectieuses' => 'Médecin généraliste',
            'pediatre' => 'Pédiatre',
            'psychiatre' => 'Psychiatre',
            'allergologue' => 'Allergologue',
            'anatomo-cyto-pathologiste' => 'Anatomo-Cyto-Pathologiste' ,
            'anesthesiste-reanimateur' => 'Anesthésiste',
            'angiologue' => 'Angiologue',
            'biochimiste' => 'Médecin biologiste',
            'biochimiste-clinique' => 'Médecin biologiste',
            'biologiste-medicale' => 'Médecin biologiste',
            'biophysique' => 'Médecin biologiste',
            'pharmacien-biologiste' => 'Médecin biologiste',
            'cancerologue' => 'Cancérologue',
            'chirurgien' => 'Chirurgien',
            'chirurgien-cancerologue' => 'Chirurgien cancérologue',
            'chirurgien-cardio-vasculaire' => 'Chirurgien cardio-vasculaire',
            'chirurgien-cardio-vasculaire-thoracique' => 'Chirurgien Cardio-Vasculaire Thoracique',
            'chirurgien-esthetique' => 'Chirurgien Esthétique',
            'chirurgien-generaliste' => 'Chirurgien Généraliste',
            'chirurgien-maxillo-facial-stomatologue' => 'Chirurgien Maxillo Facial Stomatologue',
            'chirurgien-orthopediste' => 'Chirurgien Orthopédiste',
            'chirurgien-orthopediste-traumatologue' => 'Chirurgien Orthopédiste Traumatologue',
            'chirurgien-pediatrique' => 'Chirurgien Pédiatrique',
            'chirurgien-plasticien' => 'Chirurgien Plasticien',
            'chirurgien-thoracique' => 'Chirurgien Thoracique',
            'chirurgien-urologue' => 'Chirurgien Urologue',
            'diabetologue' => 'Diabétologue',
            'dieteticien' => 'Diététicien',
            'embryologiste' => 'Embryologiste',
            'endocrinologue' => 'Endocrinologue',
            'endocrinologue-diabetologue' => 'Endocrinologue Diabétologue',
            'gastro-enterologue' => 'Gastro-entérologue et hépatologue',
            'geriatre' => 'Gériatre',
            'gynecologue' => 'Gynécologue',
            'hematologue' => 'Hématologue',
            'hematologue-clinique' => 'Hématologue',
            'hematopathologiste' => 'Hématologue',
            'imagerie-medicale' => 'IRM',
            'immunologiste' => 'Immunologue',
            'immunopathologiste' => 'Immunologue',
            'interniste' => 'Médecine Interne',
            'interniste-maladies-infectieuses'  => 'Médecine Interne',
            'interniste-reanimation-medicale' => 'Médecine Interne',
            'kinesitherapeute' => 'Kinésithérapeute',
            'medecin-du-travail' => 'Médecin du Travail',
            'medecin-esthetique' => 'Médecin esthétique',
            'medecin-legiste' => 'Médecine légale et expertises médicales',
            'medecin-nucleaire' => 'Médecin Nucléaire',
            'medecin-physique' => 'Médecin physique - Réadaptateur',
            'medecin-physique-readaptateur' => 'Médecin physique - Réadaptateur',
            'medecine-preventive' => 'Médecine Préventive',
            'microbiologiste' => 'Médecin biologiste',
            'neonatologiste' => 'Pédiatre néonatologiste',
            'nephrologue' => 'Néphrologue',
            'neurochirurgien' => 'Neurochirurgien',
            'neurologue' => 'Neurologue',
            'nutritionniste' => 'Médecin nutritionniste',
            'oncologue-radiotherapeute' => 'Oncologue-Radiothérapeute',
            'orthodontiste' => 'Orthodontiste',
            'orthopediste' => 'Orthopédiste',
            'orthopediste-traumatologue' => 'Orthopédiste',
            'orthophoniste' => 'Orthophoniste',
            'orthoptiste' => 'Orthoptiste',
            'parasitologiste' => 'Médecin biologiste',
            'pedopsychiatre' => 'Pédopsychiatre',
            'physiologiste' => 'Physiologiste',
            'physiotherapeute' => 'Kinésithérapeute',
            'pneumologue' => 'Pneumologue',
            'psychotherapeute' => 'Psychothérapeute',
            'radiologue' => 'Radiologue',
            'radiotherapeute' => 'Radiologue',
            'reanimateur-medical' => 'Médecin réanimateur médical',
            'rhumatologue' => 'Rhumatologue',
            'sexologue' => 'Sexologue',
            'stomatologue' => 'Stomatologue',
            'urologue' => 'Urologue',
        ];


        foreach ($categories as $key => $category) {
            $output->writeln('<info>'.$category.'</info>');
            $output->writeln('<info>#############################</info>');

            $firstDoctor = '';
            $break = false;
            for ($page =0; $page < 300; $page++) {
                if ($break) {
                    break;
                }
                $output->writeln('<info>'.$page.'</info>');

                $listDoctorsByCategory = $this->getDomFromUrl('medecin/'.$key.'/'.$page);
                $crawler = new Crawler($listDoctorsByCategory->getContents());
                $filter = $crawler->filter('li.doc_result_list');
                $nbElementPerPage = iterator_count($filter);

                if ($nbElementPerPage > 1) {
                    foreach ($filter as $i => $content) {
                        $crawler = new Crawler($content);
                        $doctorPageLink = $crawler->filter('.praticien__name a')->attr('href');

                        $doctorPageContent = $this->getDomFromUrl($doctorPageLink);

                        $doctorCrawler = new Crawler($doctorPageContent->getContents());
                        if (!$doctorCrawler->filter('.pf-itempage-maindiv .docinfo h1')->count()) {
                            continue;
                        }
                        $name = $doctorCrawler->filter('.pf-itempage-maindiv .docinfo h1')->text();

                        if ($page == 0) {
                            $firstDoctor = $name;
                        }

                        if ($page > 0 && $firstDoctor == $name) {
                            $break = true;
                        }
                        $firstname = '';
                        $lastname = '';
                        if ($doctorCrawler->filter('.pf-itempage-maindiv .docinfo h1')->count()) {
                            $nameData =  explode(' ',$name);
                            if (count($nameData) == 2) {
                                continue;
                            }
                            elseif (count($nameData) == 3) {
                                $firstname = $nameData[1];
                                $lastname = $nameData[2];
                            } elseif (count($nameData) == 4) {
                                $firstname = $nameData[1];
                                $lastname = $nameData[2].' '. $nameData[3];
                            }

                            elseif (count($nameData) == 5 ) {
                                $firstname = $nameData[1] . ' '. $nameData[2];
                                $lastname = $nameData[3].' '. $nameData[4];
                            } else {
                                $firstname = $nameData[1] . ' ' . $nameData[2];
                                $lastname = $nameData[3] .' '. $nameData[4] .' '. $nameData[5];
                            }
                        }

                        $doctor = $em->getRepository('DoctoubibModelsBundle:Doctor')->findOneBy(array('firstname' => $firstname, 'lastname' => $lastname));

                        if ($doctor) {
                            if ($doctorCrawler->filter('.pfdetailitem-subelement.pf-onlyitem .pfdetail-ftext')->count()) {
                                $specialities = explode('<br>', $doctorCrawler->filter('.pfdetailitem-subelement.pf-onlyitem .pfdetail-ftext')->html());
                                foreach ($specialities as $specialitySlug) {
                                    if ($specialitySlug != '') {
                                        $specialitySlug = $this->slugify($specialitySlug);
                                        $speciality = $em->getRepository('DoctoubibModelsBundle:Speciality')->findOneBy(['name' => $categories[$specialitySlug]]);
                                        if (!$speciality) {
                                            $speciality = new Speciality();
                                            $speciality->setName($categories[$specialitySlug]);
                                            $em->persist($speciality);
                                            $em->flush();
                                        }
                                    }

                                    if (!$doctor->getSpecialities()->contains($speciality)) {
                                        $doctor->addSpeciality($speciality);
                                    }
                                }

                                $em->persist($doctor);
                                $em->flush();
                            }

                        } else {
                            $doctor = new Doctor();
                            $doctor->setFirstname($firstname);
                            $doctor->setLastname($lastname);
                            $doctor->setCivility('mr');

                            if ($doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=telephone]')->count()) {
                                $telephone = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=telephone]')->text();
                                $doctor->setOfficePhoneNumber(str_replace('.', '', $telephone));
                            }

                            if($doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl .pfadmicon-glyph-351')->count()) {
                                $mobile = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl .pfadmicon-glyph-351')->parents()->text();
                                $doctor->setPhoneNumber(str_replace('.', '', $mobile));
                            }

                            if ($doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=email] a')->count()) {
                                $email = $doctorCrawler->filter('.pf-itempage-sidebarinfo-elurl span[itemprop=email] a')->attr('href');
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
                                $latitude = $doctorCrawler->filter('meta[itemprop="latitude"]')->attr('content');
                                $doctor->setLatitude($latitude);
                            }

                            if ($doctorCrawler->filter('meta[itemprop="longitude"]')->count()) {
                                $longitude = $doctorCrawler->filter('meta[itemprop="longitude"]')->attr('content');
                                $doctor->setLongitude($longitude);
                            }

                            if ($doctorCrawler->filter('.pfdetailitem-subelement.pf-onlyitem > p')->count()) {
                                $formationHtml = $doctorCrawler->filter('.pfdetailitem-subelement.pf-onlyitem > p')->html();
                                $formation = explode('<br>', $formationHtml);

                                $formation = serialize($formation);
                                $doctor->setFormation($formation);
                            }

                            if ($doctorCrawler->filter('img[src="img/cnam.jpg"]')->count()) {
                                $doctor->setInsurance(true);
                            }

                            if ($doctorCrawler->filter('.pfdetailitem-subelement.pf-onlyitem .pfdetail-ftext')->count()) {
                                $specialities = explode('<br>', $doctorCrawler->filter('.pfdetailitem-subelement.pf-onlyitem .pfdetail-ftext')->html());
                                foreach ($specialities as $specialitySlug) {
                                    if ($specialitySlug != '') {
                                        $specialitySlug = $this->slugify($specialitySlug);
                                        $speciality = $em->getRepository('DoctoubibModelsBundle:Speciality')->findOneBy(['name' => $categories[$specialitySlug]]);

                                        if (!$speciality) {
                                            $speciality = new Speciality();
                                            $speciality->setName($categories[$specialitySlug]);
                                            $em->persist($speciality);
                                            $em->flush();
                                        }

                                        if (!$doctor->getSpecialities()->contains($speciality)) {
                                            $doctor->addSpeciality($speciality);
                                        }

                                    }
                                }
                            }

                            if ($doctorCrawler->filter('.honolist ul li strong')->count()) {
                                $consultationPrice = $doctorCrawler->filter('.honolist ul li strong')->text();
                                $doctor->setConsultationPriceMin(str_replace(' DT', '', $consultationPrice));
                            }

                            $em->persist($doctor);
                            $em->flush();
                        }
                    }
                } else {
                    //throw new RuntimeException('Got empty result processing the dataset!');
                }

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


    static private function slugify($text)
    {
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);
        $text = trim($text, '-');

        if (function_exists('iconv'))
        {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        $text = strtolower($text);
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }
}
