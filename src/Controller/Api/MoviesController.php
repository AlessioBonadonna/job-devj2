<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;

class MoviesController extends AbstractController
{
    #[Route('/api/movies')]
    public function list(Connection $db, Request $request, LoggerInterface $log): Response
    {
        $json = $request->getContent();
        $data = json_decode($json, true);

        if (isset($data['filter'])) {
            $filters = $data['filter'];
        }
        if (isset($data['category'])) {
            $category = $data['category'];
        }

        $qb = $db->createQueryBuilder();
        $qb->select("m.*")
            ->from("movies", "m")
            ->setMaxResults(50);

        if (isset($category) && $category!=null && $category!='0') {
            $qb->join('m', 'movies_genres', 'cm', 'm.id = cm.movie_id')
                ->join('cm', 'genres', 'c', 'c.id = cm.genre_id')
                ->where('c.value = :category')
                ->setParameter('category', $category);
        }
        if (isset($filters) && $filters == '2') {
            $qb->orderBy('m.rating', 'DESC');
        } else {
            $qb->orderBy("m.release_date", "DESC");
        }

        $rows = $qb->executeQuery()->fetchAllAssociative();

        return $this->json([
            "movies" => $rows
        ]);
    }
}
