<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Journal;


use JuriyPanasevich\Logger\Journal\AbstractJournal;
use JuriyPanasevich\Logger\Journal\AbstractJournalLog;

class QueueFileJournalLog extends AbstractJournalLog {
    
    const TABLE_NAME = 'queue_journal_log';

    protected $journal;

    /**
     * @return QueueFileJournal
     */
    public function getJournal() {
        return $this->journal;
    }

    public function setJournal(AbstractJournal $journal) {
        $this->journal = $journal;
    }

    public function find() {
        return $this;
    }

    public function save() {
        if (!$journalId = $this->getJournal()->id) {
            throw new \Exception('Не установлен id журнала');
        }
        $date = new \DateTime();
        $sql = sprintf('insert into %s(date_create, journalId, message, code) values("%s", %s, "%s", %s)',
            self::TABLE_NAME,
            $date->format("Y-m-d H:i:s"),
            $journalId, 
            $this->getMessage(), 
            $this->getCode() ?: 'NULL');
        return $this->getJournal()->getStorage()->exec($sql);
    }
}