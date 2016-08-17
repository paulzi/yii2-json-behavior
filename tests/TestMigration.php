<?php
/**
 * @link https://github.com/paulzi/yii2-json-behavior
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-json-behavior/blob/master/LICENSE)
 */

namespace tests;

use yii\db\Schema;
use yii\db\Migration;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 */
class TestMigration extends Migration
{
    public function up()
    {
        ob_start();
        if ($this->db->getTableSchema('{{%item}}', true) !== null) {
            $this->dropTable('{{%item}}');
        }
        $this->createTable('{{%item}}', [
            'id'     => $this->primaryKey(),
            'params' => $this->text(),
        ]);

        // update cache (sqlite bug)
        $this->db->getSchema()->getTableSchema('{{%item}}', true);
        ob_end_clean();
    }
}