<?php

namespace App\Test\Controller;

use App\Entity\Panier;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PanierControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PanierRepository $repository;
    private string $path = '/panier/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Panier::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Panier index');

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
            'panier[quantite]' => 'Testing',
            'panier[prix_total]' => 'Testing',
            'panier[date_creation]' => 'Testing',
            'panier[statut]' => 'Testing',
            'panier[id_user]' => 'Testing',
            'panier[id_produit]' => 'Testing',
            'panier[id_panier]' => 'Testing',
        ]);

        self::assertResponseRedirects('/panier/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Panier();
        $fixture->setQuantite('My Title');
        $fixture->setPrix_total('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setStatut('My Title');
        $fixture->setId_user('My Title');
        $fixture->setId_produit('My Title');
        $fixture->setId_panier('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Panier');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Panier();
        $fixture->setQuantite('My Title');
        $fixture->setPrix_total('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setStatut('My Title');
        $fixture->setId_user('My Title');
        $fixture->setId_produit('My Title');
        $fixture->setId_panier('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'panier[quantite]' => 'Something New',
            'panier[prix_total]' => 'Something New',
            'panier[date_creation]' => 'Something New',
            'panier[statut]' => 'Something New',
            'panier[id_user]' => 'Something New',
            'panier[id_produit]' => 'Something New',
            'panier[id_panier]' => 'Something New',
        ]);

        self::assertResponseRedirects('/panier/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getQuantite());
        self::assertSame('Something New', $fixture[0]->getPrix_total());
        self::assertSame('Something New', $fixture[0]->getDate_creation());
        self::assertSame('Something New', $fixture[0]->getStatut());
        self::assertSame('Something New', $fixture[0]->getId_user());
        self::assertSame('Something New', $fixture[0]->getId_produit());
        self::assertSame('Something New', $fixture[0]->getId_panier());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Panier();
        $fixture->setQuantite('My Title');
        $fixture->setPrix_total('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setStatut('My Title');
        $fixture->setId_user('My Title');
        $fixture->setId_produit('My Title');
        $fixture->setId_panier('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/panier/');
    }
}
