<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;

class CategoriesController extends AbstractController
{
    #[Route('/api/categories')]
    public function categories(Connection $db, Request $request ): Response
    {
         //ottengo i dati dalla richiesta 
        $json = $request->getContent();
        $data = json_decode($json, true);

        if (isset($data['filter'])) {
            $filters = $data['filter'];
        }
        // Creo una nuova istanza di QueryBuilder per costruire la query 
        $qb = $db->createQueryBuilder();

        // Selezioniamo tutti i campi della tabella "genres"
        $qb->select("c.*")
            ->from("genres", "c");
        $rows = $qb->executeQuery()->fetchAllAssociative();
        // Ritorno i risultati come JSON nella risposta
        return $this->json([
            "categories" => $rows
        ]);
    }
}
