<?php

namespace DB;

use Aura\SqlQuery\QueryFactory;
use PDO;

class QueryBuilder
{
    const EMPTY_CONDIION = "1 = 1";

    private $rowCounts = 0;

    private $pdo;
    private $queryFactory;

    public function __construct()
    {
        $this->pdo = Connection::make();
        $this->queryFactory = new QueryFactory('mysql');
    }

    //CRUD
    //Create
    public function create($table, $data)
    {
        $insert = $this->queryFactory->newInsert();

        $insert->into($table)
            ->cols($data);

        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());

        return $this->pdo->lastInsertId();
    }

    //Read
    public function getOne($table, $value, $column = 'id')
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])
            ->from($table)
            ->where($column . "= :value")
            ->bindValues(['value' => $value]);

        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    //Update
    public function update($table, $data, $id)
    {
        $update = $this->queryFactory->newUpdate();

        $update->table($table)
            ->cols($data)
            ->where('id = :id')           // AND WHERE these conditions
            ->bindValues([                  // bind these values to the query
                'id' => $id,
            ]);

        $sth = $this->pdo->prepare($update->getStatement());
        $sth->execute($update->getBindValues());
    }

    //Delete
    public function delete($table, $id)
    {
        $delete = $this->queryFactory->newDelete();

        $delete
            ->from($table)                   // FROM this table
            ->where('id = :id')           // AND WHERE these conditions
            ->bindValues([                  // bind these values to the query
                'id' => $id,
            ]);

        $sth = $this->pdo->prepare($delete->getStatement());
        $sth->execute($delete->getBindValues());
    }

    //getAll
    public function getAll($table, $where = self::EMPTY_CONDIION, $start = 0, $limit = null)
    {
        if (empty($where)) {
            $where = self::EMPTY_CONDIION;
        }

        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])
            ->from($table)
            ->where($where);

        if ($limit && $limit != 0) {
            $select
                ->limit($limit)
                ->offset($start);
        }

        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    //Count rows
    public function countRows()
    {
        return $this->rowCounts;
    }
}