<?php

namespace OCA\SmsBackupVault\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20211001000000 extends SimpleMigrationStep {
    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('sms_address')) {
            $this->createSmsAddress($schema);
        }

        if (!$schema->hasTable('sms_thread')) {
            $this->createSmsThread($schema);
        }

        if (!$schema->hasTable('sms_thread_address')) {
            $this->createSmsThreadAddress($schema);
        }

        if (!$schema->hasTable('sms_message')) {
            $this->createSmsMessage($schema);
        }

        if (!$schema->hasTable('sms_file')) {
            $this->createSmsFile($schema);
        }

        return $schema;
    }

    private function createSmsFile($schema) {
        $table = $schema->createTable('sms_attachment');

        $table->addColumn('id', \OCP\DB\Types::INTEGER, [
            'autoincrement' => true,
            'unsigned' => true,
            'notnull' => true,
        ]);

        $table->addColumn('message_id', \OCP\DB\Types::INTEGER, [
            'notnull' => true,
            'unsigned' => true,
        ]);

        $table->addColumn('name', \OCP\DB\Types::STRING, [
            'notnull' => false,
            'length' => 200,
        ]);

        $table->addColumn('filetype', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length' => 200,
        ]);

        $table->addColumn('unique_hash', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length' => 32,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['message_id'], 'sms_file_mid_index');
        $table->addIndex(['unique_hash'], 'sms_file_hash_index');
    }

    private function createSmsMessage($schema) {
        $table = $schema->createTable('sms_message');

        $table->addColumn('id', \OCP\DB\Types::INTEGER, [
            'autoincrement' => true,
            'unsigned' => true,
            'notnull' => true,
        ]);

        $table->addColumn('thread_id', \OCP\DB\Types::INTEGER, [
            'notnull' => true,
            'unsigned' => true,
        ]);

        $table->addColumn('address_id', \OCP\DB\Types::INTEGER, [
            'notnull' => false,
            'unsigned' => true,
        ]);

        $table->addColumn('received', \OCP\DB\Types::INTEGER, [
            'notnull' => true,
            'unsigned' => true,
        ]);

        $table->addColumn('timestamp', \OCP\DB\Types::INTEGER, [
            'notnull' => true,
            'unsigned' => true,
        ]);

        $table->addColumn('body', \OCP\DB\Types::TEXT, [
            'notnull' => false,
        ]);

        $table->addColumn('unique_hash', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length' => 32
        ]);

        $table->setPrimaryKey(['id', 'thread_id']);
        $table->addIndex(['id'], 'sms_message_mid_index');
        $table->addIndex(['thread_id'], 'sms_message_tid_index');
        $table->addIndex(['address_id'], 'sms_message_aid_index');
        $table->addIndex(['unique_hash'], 'sms_message_hash_idx');
    }

    private function createSmsThread($schema) {
        $table = $schema->createTable('sms_thread');
        $table->addColumn('id', \OCP\DB\Types::INTEGER, [
            'autoincrement' => true,
            'unsigned' => true,
            'notnull' => true,
        ]);

        $table->addColumn('user_id', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length'  => 64,
        ]);

        $table->addColumn('name', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length' => 200
        ]);

        $table->addColumn('unique_hash', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length' => 32
        ]);

        $table->setPrimaryKey(['id', 'user_id']);
        $table->addIndex(['id'], 'sms_thread_tid_index');
        $table->addIndex(['unique_hash'], 'sms_thread_hash_idx');
    }

    private function createSmsThreadAddress($schema) {
        $table = $schema->createTable('sms_thread_address');
        $table->addColumn('thread_id', \OCP\DB\Types::INTEGER, [
            'notnull' => true,
            'unsigned' => true,
        ]);

        $table->addColumn('address_id', \OCP\DB\Types::INTEGER, [
            'notnull' => true,
            'unsigned' => true,
        ]);

        $table->setPrimaryKey(['thread_id', 'address_id']);
        $table->addIndex(['thread_id'], 'sms_ta_tid_index');
        $table->addIndex(['address_id'], 'sms_ta_aid_index');
    }

    private function createSmsAddress($schema) {
        $table = $schema->createTable('sms_address');

        $table->addColumn('id', \OCP\DB\Types::INTEGER, [
            'autoincrement' => true,
            'unsigned' => true,
            'notnull' => true,
        ]);

        $table->addColumn('user_id', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length'  => 64,
        ]);

        $table->addColumn('address', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length' => 200
        ]);

        $table->addColumn('name', \OCP\DB\Types::STRING, [
            'notnull' => false,
            'length' => 200,
        ]);

        $table->setPrimaryKey(['id', 'user_id']);
        $table->addIndex(['address'], 'sms_address_value_index');
        $table->addIndex(['id'], 'sms_address_aid_index');
    }
}
