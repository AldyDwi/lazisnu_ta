<?php

namespace App\Models;

use CodeIgniter\Model;

class DatabaseModel extends Model
{
 
    /**
     * Retrieves a single row of data from the database by the given id.
     *
     * @param int $id The id of the row to be retrieved.
     * @return object|null The row of data that matches the given id, or null if no such row exists.
     * @example get one data using getRowObject() and get more than using getResultObject()
     */
    public static function getData(string $table, array $params = [], string $orderBy = '')
    {
        try {
            $db = \Config\Database::connect();
            $db = $db->table($table);

            foreach ($params as $key => $value) {
                $db->where($key, $value);
            }

            if ($orderBy) {
                $db->orderBy($orderBy);
            }

            return $db->get();
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            throw new \Exception("Error retrieving data.");
        }
    }

    /**
     * Inserts a new data into the database.
     *
     * @param string $table table name.
     * @param array $data The data to be inserted.
     * @return int The ID of the inserted data.
     */
    public static function insertData(string $table, array $data)
    {
        try {
            $db = \Config\Database::connect();

            $builder = $db->table($table);
            $builder->insert($data);
            $lastId = $db->insertID();

            return $lastId;
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            throw new \Exception("Error inserting data.");
        }
    }


    /**
     * Updates the data for a specified id.
     *
     * @param string $table table name.
     * @param array $data The data to update.
     * @param array $array The where condition of the data to update.
     *
     * @return int The number of rows affected by the update.
     */
    public static function updateData(string $table, array $where, array $data)
    {
        try {
            $db = \Config\Database::connect();
            $builder = $db->table($table);

            if (empty($where)) {
                return $builder->update($data);
            }

            $builder->where($where);
            $result = $builder->update($data);

            return $result;
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            throw new \Exception("Error updating data.");
        }
    }


    /**
     * Deletes the data for a specified id.
     *
     * @param string $table table name.
     * @param int $id The id of the data to delete.
     *
     * @param array $array The where condition of the data to update.
     */
    public static function deleteData(string $table, array $where)
    {
        $db = \Config\Database::connect();

        $db->transBegin();
        try {
            $builder = $db->table($table);
            $builder->where($where);
            $result = $builder->delete();
            $db->transCommit();

            return $result;
        } catch (\Throwable $th) {
            $db->transRollback();

            $dbError = $db->error();
            if (!empty($dbError['message'])) {
                // log_message('error', $dbError['message']);
                throw new \Exception($dbError['message']);
            }
            // log_message('error', $th->getMessage());
            throw new \Exception("Delete data failed.");
        }
    }

    /**
     * Updates multiple rows in the table based on a given condition.
     *
     * @param array $query to get data.
     *
     * @return object The number of rows affected by the update.
     * 
     * @example get one data using getRowObject() and get more than using getResultObject()
     */
    public static function get(array $query, string $otherDb = '')
    {
        $db_connect = \Config\Database::connect();
        if ($otherDb) {
            $db_connect = \Config\Database::connect($otherDb);
        }
        $db = $db_connect->table($query['from']);

        //decleare select
        if (isset($query['select'])) {
            $db->select($query['select']);
        }
        //deceleare join
        if (isset($query['join'])) {
            foreach ($query['join'] as $key => $item_join) {
                $explode_item_join = explode(', ', $item_join);

                //param 1
                isset($explode_item_join[0]) ? $param_1 = $explode_item_join[0] : $param_1 = '';
                //param 2
                isset($explode_item_join[1]) ? $param_2 = $explode_item_join[1] : $param_2 = '';
                //param 3
                isset($explode_item_join[2]) ? $param_3 = $explode_item_join[2] : $param_3 = '';

                $db->join($param_1, $param_2, $param_3);
            }
        }
        if (isset($query['join_custom'])) {
            foreach ($query['join_custom'] as $table_name => $item_join) {
                $explode_item_join = explode(',', $item_join);
                $last_param = end($explode_item_join);
                $value_param = str_replace(',' . $last_param, ' ', $item_join);
                $db->join($table_name, $value_param, $last_param);
            }
        }
        //decleare where 
        if (isset($query['where'])) {
            $db->where($query['where']);
        }

        if (isset($query['or_where'])) {
            $db->orWhere($query['or_where']);
        }

        //define where in
        if (isset($query['where_in'])) {
            foreach ($query['where_in'] as $field_name => $array_list) {
                $db->whereIn($field_name, $array_list);
            }
        }
        //define where not in
        if (isset($query['where_not_in'])) {
            foreach ($query['where_not_in'] as $field_name => $array_list) {
                $db->whereNotIn($field_name, $array_list);
            }
        }
        //define not like
        if (isset($query['not_like'])) {
            foreach ($query['not_like'] as $field_name => $item_not_like) {
                $explode_item_not_like = explode(',', $item_not_like);
                if (count($explode_item_not_like) > 1) {
                    $param2 = end($explode_item_not_like);
                    $param1 = substr($item_not_like, 0, strlen($item_not_like) - (strlen($param2) + 1));
                    //add to query
                    $db->notLike($field_name, $param1, $param2);
                } else {
                    $db->notLike($field_name, $item_not_like);
                }
            }
        }
        //define like
        if (isset($query['like'])) {
            foreach ($query['like'] as $field_name => $item_like) {
                $explode_item_like = explode(',', $item_like);
                if (count($explode_item_like) >= 1) {
                    $param2 = end($explode_item_like);
                    $param1 = substr($item_like, 0, strlen($item_like) - (strlen($param2) + 1));
                    //add to query
                    $db->like($field_name, $param1, $param2);
                } else {
                    $db->like($field_name, $item_like);
                }
            }
        }
        //define or_like
        if (isset($query['or_like'])) {
            foreach ($query['or_like'] as $field_name => $item_like) {
                $explode_item_like = explode(',', $item_like);
                if (count($explode_item_like) >= 1) {
                    $param2 = end($explode_item_like);
                    $param1 = substr($item_like, 0, strlen($item_like) - (strlen($param2) + 1));
                    //add to query
                    $db->orLike($field_name, $param1, $param2);
                } else {
                    $db->orLike($field_name, $item_like);
                }
            }
        }
        //decleare order by 
        if (isset($query['order_by'])) {
            $explode_order_by = explode(',', $query['order_by']);
            if (count($explode_order_by) > 1) {
                $param2 = end($explode_order_by);
                $param1 = substr($query['order_by'], 0, strlen($query['order_by']) - (strlen($param2) + 1));
                $db->orderBy($param1, $param2);
            } else {
                $db->orderBy($query['order_by']);
            }
        }
        //decleare group by
        if (isset($query['group_by'])) {
            $db->groupBy($query['group_by']);
        }
        //decleare limit 
        if (isset($query['limit'])) {
            if (is_array($query['limit'])) {
                //when array data
                //decide use 
                if (isset($query['limit']['limit']) && isset($query['limit']['start'])) {
                    //use both
                    $db->limit($query['limit']['limit'], $query['limit']['start']);
                } else {
                    if (isset($query['limit']['limit'])) {
                        $db->limit($query['limit']['limit']);
                    }
                }
            } else {
                //when not array
                $db->limit($query['limit']);
            }
        }
        //final deleare using get
        return $db->get();
    }
}
