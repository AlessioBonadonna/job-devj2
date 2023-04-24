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
    public function list(Connection $db, Request $request ): Response
    {
        //ottengo i dati dalla richiesta 
        $json = $request->getContent();
        $data = json_decode($json, true);

        // Verifichiamo se è presente l'ordinamento  nella richiesta e la salvo in una variabile
        if (isset($data['filter'])) {
            $filters = $data['filter'];
        }
        // Verifichiamo se è presente la categoria nella richiesta e la salvo in una variabile
        if (isset($data['category'])) {
            $category = $data['category'];
        }
        // Creo una nuova istanza di QueryBuilder per costruire la query SQL
        $qb = $db->createQueryBuilder();
        //Selezioniamo tutti i campi della tabella "movies"
        $qb->select("m.*")
            ->from("movies", "m")
            ->setMaxResults(50);

        //join tra le tabelle "movies_genres" e "genres" per ottenere i film della categoria specificata
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

        // Eseguo la query e otteniamo i risultati come array associativo
        $rows = $qb->executeQuery()->fetchAllAssociative();

        // Ritorno i risultati come JSON nella risposta
        return $this->json([
            "movies" => $rows
        ]);
    }
}
