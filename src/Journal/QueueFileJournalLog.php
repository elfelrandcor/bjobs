<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Journal;


use JuriyPanasevich\Logger\Journal\AbstractJournal;
use JuriyPanasevich\Logger\Journal\AbstractJournalLog;

class QueueFileJournalLog extends AbstractJournalLog {

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
        return file_put_contents($this->getJournal()->getStorage(), sprintf('%s: (%s) %s%s', (new \DateTime())->format('d.m.Y H:i:s'), $this->getCode() ?: '–', $this->getMessage(), PHP_EOL), FILE_APPEND);
    }
}