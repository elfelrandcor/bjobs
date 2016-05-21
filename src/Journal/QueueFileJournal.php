<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Journal;


use JuriyPanasevich\BJobs\Queue;
use JuriyPanasevich\Logger\Exceptions\JournalException;
use JuriyPanasevich\Logger\Journal\AbstractJournal;
use JuriyPanasevich\Logger\Journal\AbstractJournalLog;
use SQLite3;

class QueueFileJournal extends AbstractJournal {

    const TABLE_NAME = 'queue_journal';

    public $id;
    public $logs;
    /** @var  Queue */
    protected $entity;
    protected $storage;

    public function __construct($path) {
        $this->storage = new SQLite3($path);
        $this->storage->busyTimeout(5000);
        // WAL mode has better control over concurrency.
        // Source: https://www.sqlite.org/wal.html
        $this->storage->exec('PRAGMA journal_mode = wal;');
    }

    public function setEntity($entity) {
        $this->entity = $entity;
    }

    public function getEntity() {
        return $this->entity;
    }

    /** @return AbstractJournalLog */
    public function getJournalLog() {
        $log = new QueueFileJournalLog();
        $log->setJournal($this);
        return $log;
    }

    public function find() {
        $t = self::TABLE_NAME;
        $name = $this->getEntity()->getName();
        if (!$result = $this->storage->query("select id from {$t} where name = '{$name}';")) {
            throw new JournalException('Ошибка выполнения запроса');
        }
        if ($result = $result->fetchArray(SQLITE3_ASSOC)) {
            $this->id = $result['id'];
            $this->fillLogs();

            return $this;
        }
        return null;
    }

    public function save() {
        $t = self::TABLE_NAME;
        $name = $this->getEntity()->getName();
        if ($this->id) {
            return true;
        } else {
            if (!$this->storage->exec("insert into {$t}(name) values('{$name}');")) {
                throw new JournalException('Журнал не сохранен');
            }
            $this->id = $this->storage->lastInsertRowID();
        }
        return true;
    }

    /**
     * @return SQLite3
     */
    public function getStorage() {
        return $this->storage;
    }

    private function fillLogs() {
        if (!$queryResult = $this->storage->query(sprintf('select * from %s where journalId = %s', QueueFileJournalLog::TABLE_NAME, $this->id))) {
            return $this;
        }
        while($result = $queryResult->fetchArray(SQLITE3_ASSOC)) {
            $log = new QueueFileJournalLog();
            $log->setJournal($this);
            $log->setCode($result['code']);
            $log->setMessage($result['message']);
            $this->logs[] = $log;
        }
        return $this;
    }
}