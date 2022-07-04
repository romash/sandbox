<?php

namespace Game\DB;

class Marketplace extends DBTable {

    public function getOfferById(int $id): ?array {
        return $this->fetchRow('SELECT * FROM marketplace WHERE id = :i1', $id);
    }

    /**
     * All active marketplace offers of the village
     */
    public function getAllOffersInVillage(int $did): array {
        return (array)$this->fetchAll('SELECT * FROM marketplace WHERE did = :i1', $did);
    }

    /**
     * All active marketplace offers from other players
     */
    public function getAllForeignOffers(int $uid): array {
        return (array)$this->fetchAll('
            SELECT marketplace.*, p.name FROM marketplace 
            LEFT JOIN player ON player.uid = marketplace.uid
            WHERE marketplace.uid <> :i1', $uid);
    }

    /**
     * Proves given offer is from the uid
     */
    public function isOfferFromUser($id, $uid): bool {
        return $this->fetchOne('SELECT COUNT(id) FROM marketplace WHERE id = :i1 AND uid = :i2', $id, $uid) > 0;
    }

    public function deleteOfferById(int $id): int {
        return $this->delete('DELETE FROM marketplace WHERE id = :i1', $id);
    }

    public function createOffer(array $data, $uid, $villageId): int {
        $ratio = round($data['m1'] / $data['m2']);
        return $this->insert('
            INSERT INTO marketplace (uid, did, rid1, rid2, m1, m2, ratio) 
            VALUES (:i1, :i2, :i3, :i4, :i5, :i6, :f7)',
            $uid, $villageId, $data['rid1'], $data['rid2'], $data['m1'], $data['m2'], $ratio
        );
    }
}
