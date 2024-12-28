<?php 
require_once "Entity.php";
//Not an actual model
abstract class AbstractUserData extends EntityData{
    public int|null $user_id;

    function __construct($user_id = null){
        $this->user_id = $user_id;
    }
}

abstract class AbstractUser extends Entity{
    public static function getByUser(int $user_id): AbstractUserData|false{
        $query = "SELECT * FROM " . static::class . " WHERE user_id = ? LIMIT 1";
        self::printQuery($query, [$user_id]);

        $stm = self::$conn->prepare($query);
        $stm->setFetchMode(PDO::FETCH_CLASS, static::class . "Data");
        $stm->execute([$user_id]);
        return $stm->fetch();
    }

    abstract public static function getIdColumn();

    //Gets the id of the user from the specialized table
    public static function getSpecializedId(int $user_id){
        $data = static::getByUser($user_id);
        $id_column = static::getIdColumn();
        return $data->$id_column;
    }
}
?>