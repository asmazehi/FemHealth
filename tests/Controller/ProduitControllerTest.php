<?php

namespace App\Test\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProduitControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ProduitRepository $repository;
    private string $path = '/produit/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Produit::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Produit index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'produit[Nom]' => 'Testing',
            'produit[Prenom]' => 'Testing',
            'produit[Prix]' => 'Testing',
            'produit[Taux_remise]' => 'Testing',
            'produit[Categorie]' => 'Testing',
            'produit[image]' => 'Testing',
            'produit[Description]' => 'Testing',
            'produit[sponsor]' => 'Testing',
            'produit[ip_produit]' => 'Testing',
        ]);

        self::assertResponseRedirects('/produit/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Produit();
        $fixture->setNom('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setPrix('My Title');
        $fixture->setTaux_remise('My Title');
        $fixture->setCategorie('My Title');
        $fixture->setImage('My Title');
        $fixture->setDescription('My Title');
        $fixture->setSponsor('My Title');
        $fixture->setIp_produit('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Produit');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Produit();
        $fixture->setNom('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setPrix('My Title');
        $fixture->setTaux_remise('My Title');
        $fixture->setCategorie('My Title');
        $fixture->setImage('My Title');
        $fixture->setDescription('My Title');
        $fixture->setSponsor('My Title');
        $fixture->setIp_produit('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'produit[Nom]' => 'Something New',
            'produit[Prenom]' => 'Something New',
            'produit[Prix]' => 'Something New',
            'produit[Taux_remise]' => 'Something New',
            'produit[Categorie]' => 'Something New',
            'produit[image]' => 'Something New',
            'produit[Description]' => 'Something New',
            'produit[sponsor]' => 'Something New',
            'produit[ip_produit]' => 'Something New',
        ]);

        self::assertResponseRedirects('/produit/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getPrenom());
        self::assertSame('Something New', $fixture[0]->getPrix());
        self::assertSame('Something New', $fixture[0]->getTaux_remise());
        self::assertSame('Something New', $fixture[0]->getCategorie());
        self::assertSame('Something New', $fixture[0]->getImage());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getSponsor());
        self::assertSame('Something New', $fixture[0]->getIp_produit());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Produit();
        $fixture->setNom('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setPrix('My Title');
        $fixture->setTaux_remise('My Title');
        $fixture->setCategorie('My Title');
        $fixture->setImage('My Title');
        $fixture->setDescription('My Title');
        $fixture->setSponsor('My Title');
        $fixture->setIp_produit('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/produit/');
    }
}
