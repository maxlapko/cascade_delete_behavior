Cascade delete behavior for Yii 

## Getting started

Put files to protected/components or extensions directory

### Configuring Model

```php

class User extends CActiveRecord
{
    // ....... 
    
    public function behaviors()
    {
        return array(
            'CascadeDelete' => array(
                'class'     => 'ext.cascadedeletebehavior.CascadeDeleteBehavior',
                'relations' => array(
                    'comments' => array('type' => CascadeDeleteBehavior::TYPE_ALL), //delete all - one query
                    'profile',
                    'posts' //every user's post will delete such as $post->delete()
                )
            ),
        );
    }

}

```
