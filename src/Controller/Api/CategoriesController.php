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
    public function list(Connection $db, Request $request, LoggerInterface $log): Response
    {
        $json = $request->getContent();
        $data = json_decode($json, true);
        if (isset($data['filter'])) {
            $filters = $data['filter'];
        }
        $qb = $db->createQueryBuilder();
        $qb->select("c.*")
            ->from("genres", "c");
        $rows = $qb->executeQuery()->fetchAllAssociative();
        return $this->json([
            "categories" => $rows
        ]);
    }
}
