<?php
namespace  ImiApp\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Action;
use Imi\Db\Db;

/**
 * @Controller ("/db/")
 * Class DbController
 * @package ImiApp\ApiServer\Controller
 */
class DbController extends HttpController {

    /**
     * @Action
     *
     * @return void
     */
    public function create($a) {
        $db = Db::getInstance();
        $statement = $db->prepare("insert into db_test(a) values (:a)");
        $statement->execute([
            ":a" => $a,
        ]);
        return [
            'id' => $statement->lastInsertId(),
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function update($id,$a) {
        $db = Db::getInstance();
        $statement = $db->prepare("update db_test set a=:a where id=:id");
        $statement->execute([
            ':id' => $id,
            ":a" => $a,
        ]);
        return [
            'rowCount' => $statement->rowCount(),
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function get($id) {
        $db = Db::getInstance();
        $statement = $db->prepare("select * from db_test where id=:id");
        $statement->execute([
            ':id' => $id,
        ]);

        return [
            'data' => $statement->fetch(),
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function query($query) {
        $db = Db::getInstance();
        $statement = $db->query("select * from db_test");

        return [
            'list' => $statement->fetchAll(),
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function delete($id) {
        $db = Db::getInstance();
        $statement = $db->prepare("delete from db_test where id=:id");
        $statement->execute([
            ':id' => $id,
        ]);
        return [
            'rowCount' => $statement->rowCount(),
        ];
    }




}