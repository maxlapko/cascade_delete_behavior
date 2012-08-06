<?php

/**
 * @author mlapko <maxlapko@gmail.com>  
 */
class CascadeDeleteBehavior extends CActiveRecordBehavior
{
    const TYPE_ALL  = 'all';
    const TYPE_EACH = 'each';
    
    /**
     * array(
     *  'posts' => array('type' => CascadeDeleteBehavior::TYPE_ALL),
     *  'category'
     *  
     * )
     * @var array 
     */
    public $relations = array();
    
    /**
     *
     * @param CModelEvent $event event parameter
     * @return boolean 
     */
    public function beforeDelete($event)
    {
        $ownerRelations = $owner->relations();
        foreach ($this->relations as $key => $value) {
            if (is_array($value)) {
                $relation = $ownerRelations[$key];
                $relation['_relation'] = $key;
                $this->_deleteByParams($relation, $value); 
            } else {
                $this->_deleteRelation($value);
            }            
        }
        return true;
    }
    
    /**
     * Cascade delete relations
     * 
     * @param array $relation
     * @param array $params 
     */
    protected function _deleteByParams($relation, $params)
    {
        if (empty($params['type']) || !in_array($params['type'], array(self::TYPE_ALL, self::TYPE_EACH))) {
            throw new CException('Incorrect value for "type" param.');
        }
        if ($params['type'] === self::TYPE_EACH) {
            $this->_deleteRelation($relation['_relation']);
        } else {
            if ($relation[0] === CActiveRecord::MANY_MANY) {
                throw new CException('Not supported MANY MANY associations.');
            }
            $relationObject = CActiveRecord::model($relation[1]);
            list($condition, $params) = $this->_prepareConditionAndParams($relation);
            $relationObject->deleteAll($condition, $params);
        }            
    }
    
    /**
     * Delete each relation
     * 
     * @param string $relation 
     */
    protected function _deleteRelation($relation) 
    {        
        $objects = $this->getOwner()->getRelated($relation);
        if ($objects !== null) {
            if (is_array($objects)) {
                foreach ($objects as $object) {
                    $object->delete();
                }
            } else {
                $objects->delete();
            }
        }        
    }
    
    /**
     * Prepare condition and params for delete
     * @param array $relation
     * @return array 
     */
    protected function _prepareConditionAndParams($relation)
    {
        $condition = $relation[2] . ' = :_pk';
        if (isset($relation['on'])) {
            $condition .= ' AND ' . $relation['on']; 
        }
        if (isset($relation['condition'])) {
            $condition .= ' AND ' . $relation['condition']; 
        }
        $params = array(':_pk' => $this->getOwner()->getPrimaryKey());
        if (isset($relation['params'])) {
            $params = array_merge($params, $relation['params']);
        }
        return array($condition, $params);
    }
    

}