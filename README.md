Cascade delete behavior for Yii 

## Getting started

Put files to protected/components or extensions directory

### Configuring config and model

```php

// main.php
'import' => array(
    // ........
    'ext.cascade_delete_behavior.*',
),


class User extends CActiveRecord
{
    // ....... 
    
    public function behaviors()
    {
        return array(
            'CascadeDelete' => array(
                'class'     => 'ext.cascade_delete_behavior.CascadeDeleteBehavior',
                'relations' => array(
                    'comments' => array('type' => CascadeDeleteBehavior::TYPE_ALL), //delete all - one query
                    'profile',
                    'posts' //every user's post will delete such as $post->delete()
                    'groups' => array(
                        'type'       => CascadeDeleteBehavior::TYPE_ALL,
                        'command'    => CascadeDeleteBehavior::COMMAND_UPDATE_ALL,
                        'attributes' => array('is_deleted' => 1),
                    )
                )
            ),
        );
    }

}

```
