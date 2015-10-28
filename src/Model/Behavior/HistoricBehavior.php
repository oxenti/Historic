<?php
namespace Request\Model\Behavior;

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
        $foreignKey = Inflector::singularize($this->_table->table()) . '_id';
        $this->_table->hasMany('Historics', [
            'foreignKey' => $foreignKey,
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
        $historicOld = $this->_table->Historics->find()->where(['request_id' => $entity->id])->toArray();
        $this->_table->Historics->patchEntity($historicOld[0], ['is_active' => 0]);
        foreach ($fields as $key => $value) {
            $historic[$value] = $entity->get($value);
        }
        $entity->set('historics', [$this->_table->Historics->newEntity($historic), $historicOld[0]]);
        $entity->dirty('historics', true);
        // debug($entity);
        //    die();
    }
}
