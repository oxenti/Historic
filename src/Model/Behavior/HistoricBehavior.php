<?php
namespace Historic\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

/**
 * Historic behavior
 */
class HistoricBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'class' => '',
        'fields' => [],
    ];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->foreignKey = Inflector::singularize($this->_table->table()) . '_id';
        $this->_table->hasMany('Historics', [
            'foreignKey' => $this->foreignKey,
            'conditions' => ['Historics.is_active' => true],
            'className' => $this->config()['class']
        ]);
    }

    /**
     * save
     */
    public function beforeSave(Event $event, Entity $entity)
    {
        $config = $this->config();
        $fields = $config['fields'];
        foreach ($fields as $key => $value) {
            $historic[$value] = $entity->get($value);
        }
        if (!$entity->isNew() && $historicOld = $this->_table->Historics->find()->where([$this->foreignKey => $entity->id])->toArray()) {
            $this->_table->Historics->patchEntity($historicOld[0], ['is_active' => 0]);
            $entity->set('historics', [$this->_table->Historics->newEntity($historic), $historicOld[0]]);
        } else {
            $entity->set('historics', [$this->_table->Historics->newEntity($historic)]);
        }
        $entity->dirty('historics', true);
    }

    /**
     * initialImport methode
     *  import the data initial in the table
     */
    protected function initialImport()
    {
        if (Configure::read('historic_behavior.' . $this->config()['class'] . '.initialImport')) {
            return;
        } elseif (!$this->_table->find()->count() || $this->_table->Historics->find()->count()) {
            return;
        } else {
            $fields = $this->config()['fields'];
            $tableEntities = $this->_table->find()->toArray();
            foreach ($tableEntities as $key => $entity) {
                foreach ($fields as $key => $value) {
                    $historic[$value] = $entity->get($value);
                }
                $historicData[] = $historic;
            }
            $historicEntities = $this->_table->Historics->newEntities($historicData);
            debug($historicEntities);
            Configure::write('historic_behavior.' . $this->config()['class'] . '.initialImport', false);
            foreach ($historicEntities as $key => $historicEntity) {
                if (!$this->_table->Historics->save($historicEntity)) {
                    Configure::write('historic_behavior.' . $this->config()['class'] . '.initialImport', true);
                    Configure::dump('historic', 'default', ['historic_behavior']);
                    return;
                }
            }
            Configure::dump('historic', 'default', ['historic_behavior']);
        }
    }
}
