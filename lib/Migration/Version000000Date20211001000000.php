<?php

/**
 * @copyright 2022 Justin Osborne <justin@eblah.com>
 * 
 * @author Justin Osborne <justin@eblah.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\MessageVault\Migration;

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

		if (!$schema->hasTable('msgvault_address')) {
			$this->createSmsAddress($schema);
		}

		if (!$schema->hasTable('msgvault_thread')) {
			$this->createSmsThread($schema);
		}

		if (!$schema->hasTable('msgvault_thread_address')) {
			$this->createSmsThreadAddress($schema);
		}

		if (!$schema->hasTable('msgvault_message')) {
			$this->createSmsMessage($schema);
		}

		if (!$schema->hasTable('msgvault_attachment')) {
			$this->createSmsFile($schema);
		}

		return $schema;
	}

	private function createSmsFile(ISchemaWrapper $schema): void {
		$table = $schema->createTable('msgvault_attachment');

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

		$table->addColumn('width', \OCP\DB\Types::INTEGER, [
			'notnull' => false,
			'unsigned' => true,
		]);

		$table->addColumn('height', \OCP\DB\Types::INTEGER, [
			'notnull' => false,
			'unsigned' => true,
		]);

		$table->addColumn('unique_hash', \OCP\DB\Types::STRING, [
			'notnull' => true,
			'length' => 32,
		]);

		$table->setPrimaryKey(['id'], 'mv_f_primary');
		$table->addIndex(['message_id'], 'mv_file_mid_index');
		$table->addIndex(['unique_hash'], 'mv_file_hash_index');
	}

	private function createSmsMessage(ISchemaWrapper $schema): void {
		$table = $schema->createTable('msgvault_message');

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

		$table->setPrimaryKey(['id', 'thread_id'], 'mv_m_primary');
		$table->addIndex(['id'], 'mv_message_mid_index');
		$table->addIndex(['thread_id'], 'mv_message_tid_index');
		$table->addIndex(['address_id'], 'mv_message_aid_index');
		$table->addUniqueIndex(['unique_hash'], 'mv_message_hash_idx');
	}

	private function createSmsThread(ISchemaWrapper $schema): void {
		$table = $schema->createTable('msgvault_thread');
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
			'notnull' => false,
			'length' => 200
		]);

		$table->addColumn('unique_hash', \OCP\DB\Types::STRING, [
			'notnull' => true,
			'length' => 32
		]);

		$table->setPrimaryKey(['id', 'user_id'], 'mv_t_primary');
		$table->addIndex(['id'], 'mv_thread_tid_index');
		$table->addUniqueIndex(['unique_hash'], 'mv_thread_hash_idx');
	}

	private function createSmsThreadAddress(ISchemaWrapper $schema): void {
		$table = $schema->createTable('msgvault_thread_address');
		$table->addColumn('thread_id', \OCP\DB\Types::INTEGER, [
			'notnull' => true,
			'unsigned' => true,
		]);

		$table->addColumn('address_id', \OCP\DB\Types::INTEGER, [
			'notnull' => true,
			'unsigned' => true,
		]);

		$table->setPrimaryKey(['thread_id', 'address_id'], 'mv_ta_primary');
		$table->addIndex(['thread_id'], 'mv_ta_tid_index');
		$table->addIndex(['address_id'], 'mv_ta_aid_index');
	}

	private function createSmsAddress(ISchemaWrapper $schema): void {
		$table = $schema->createTable('msgvault_address');

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

		$table->setPrimaryKey(['id', 'user_id'], 'mv_addr_primary');
		$table->addIndex(['address'], 'mv_addr_value_index');
		$table->addIndex(['id'], 'mv_addr_aid_index');
	}
}
