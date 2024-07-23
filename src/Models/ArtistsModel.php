<?php

declare(strict_types=1);

require_once('src/Entities/Artist.php');
require_once('src/Entities/AlbumArtWork.php');
require_once('src/Entities/AlbumCount.php');

class ArtistsModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getArtistsAlbumCount(int $artistId): AlbumCount
    {
        $query = $this->db->prepare("SELECT COUNT(`albums`.`id`) AS 'album_count'
                                                FROM `artists`
                                                    INNER JOIN `albums`
                                                        ON `artists`.`id` = `albums`.`artist_id`
                                                            WHERE `artists`.`id` = :artistId;");
        $query->setFetchMode(PDO::FETCH_CLASS, AlbumCount::class);
        $query->execute(['artistId' => $artistId]);
        return $query->fetch();
    }

    /**
     * @return Artist[] array
     */
    public function getArtistsSummary(): array
    {
        $query = $this->db->prepare('SELECT `artists`.`id`, `artists`.`artist_name`
                                                FROM `artists` ORDER BY RAND() LIMIT 3;');
        $query->setFetchMode(PDO::FETCH_CLASS, Artist::class);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * @return Artist[] array
     */
    public function getAllArtists(): array
    {
        $query = $this->db->prepare('SELECT `artists`.`id`, `artists`.`artist_name`
                                                FROM `artists`;');
        $query->setFetchMode(PDO::FETCH_CLASS, Artist::class);
        $query->execute();
        return $query->fetchAll();
    }

    public function getArtistAlbumArtworks(int $artistId): array
    {
        $query = $this->db->prepare('SELECT `artists`.`id`, `albums`.`artwork_url`
                                                FROM `artists`
                                                    INNER JOIN `albums`
                                                        ON `artists`.`id` = `albums`.`artist_id`
                                                            WHERE `artists`.`id` = :artistId
                                                                    LIMIT 2;');
        $query->setFetchMode(PDO::FETCH_CLASS, AlbumArtWork::class);
        $query->execute(['artistId' => $artistId]);
        return $query->fetchAll();
    }

    /**
     * @param string $artistID
     * @return Artist[]
     */
    public function getArtistSongsAlbumByID(int $artistID, int $artistAlbum):Array
    {
        $query = $this->db->prepare("SELECT `artist_name`, `album_name`, `artwork_url`, `song_name`, `length` , `songs`.`id` AS 'songID'
            FROM `albums`
                INNER JOIN `artists`
                    ON `artists`.`id` = `albums`.`artist_id`
                INNER JOIN `songs`
                    ON `songs`.`album_id` = `albums`.`id`
                    WHERE `artists`.`id` = :artistID AND `album_id` = :artistAlbum
                    ORDER BY `artist_name`,`album_name`;");
        $query->setFetchMode(PDO::FETCH_CLASS,Artist::class);
        $query->execute(['artistID'=> $artistID, 'artistAlbum'=> $artistAlbum]);
        return $query->fetchAll();
    }

    /**
     * @param $artistID
     * @return Artist[]
     */
    public function getArtistAlbumList($artistID):Array
    {
        $query = $this->db->prepare("SELECT `album_name`
            FROM `albums`
                INNER JOIN `artists`
                    ON `artists`.`id` = `albums`.`artist_id`
                INNER JOIN `songs`
                    ON `songs`.`album_id` = `albums`.`id`
                    WHERE `artists`.`id` = :artistID
                    GROUP BY `album_name`
                    ORDER BY `artist_name`,`album_name`;");
        $query->setFetchMode(PDO::FETCH_CLASS,Artist::class);
        $query->execute(['artistID'=> $artistID]);
        return $query->fetchAll();
    }
}