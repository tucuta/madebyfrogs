<?php

/**
 * class Page
 *
 * Enter description here...
 *
 * @author Philippe Archambault <philippe.archambault@gmail.com>
 * @since  0.1
 */


class Page extends Record
{
    const TABLE_NAME = 'page';
    
    const STATUS_DRAFT = 1;
    const STATUS_REVIEWED = 50;
    const STATUS_PUBLISHED = 100;
    const STATUS_HIDDEN = 101;

    public $title;
    public $slug;
    public $breadcrumb;
    public $content;
    public $parent_id;
    public $layout_id;
    public $behavior_id;
    public $status_id;
    
    public $created_on;
    public $updated_on;
    public $created_by_id;
    public $updated_by_id;
    
    public function beforeInsert()
    {
        $this->created_by_id = AuthUser::getId();
        $this->created_on = date('Y-m-d H:i:s');
        
        // build slug path
        if ($parent = Record::findByIdFrom('Page', $this->parent_id)) {
            $this->slug = trim($parent->slug.'/'.$this->slug, '/');
        }
        
        return true;
    }
    
    public function beforeUpdate()
    {
        $this->updated_by_id = AuthUser::getId();
        $this->updated_on = date('Y-m-d H:i:s');
        
        // get old page information (slug to replace)
        $old_page = self::findById($this->id);
        
        // rebuild slug path
        $parent = Record::findByIdFrom('Page', $this->parent_id);
        $my_slug = self::getSlug($this->slug);
        
        if ($parent) {
            $my_slug = trim($parent->slug.'/'.$my_slug, '/');
        }
        
        if (Page::hasChildren($this->id)) {
            Page::replacePath($old_page->slug, $my_slug);
        }
        
        $this->slug = $my_slug;
        
        return true;
    }
    
    public static function find($args = null)
    {
        
        // Collect attributes...
        $where    = isset($args['where']) ? trim($args['where']) : '';
        $order_by = isset($args['order']) ? trim($args['order']) : '';
        $offset   = isset($args['offset']) ? (int) $args['offset'] : 0;
        $limit    = isset($args['limit']) ? (int) $args['limit'] : 0;

        // Prepare query parts
        $where_string = empty($where) ? '' : "WHERE $where";
        $order_by_string = empty($order_by) ? '' : "ORDER BY $order_by";
        $limit_string = $limit > 0 ? "LIMIT $offset, $limit" : '';

        $tablename = self::tableNameFromClassName('Page');
        $tablename_user = self::tableNameFromClassName('User');

        // Prepare SQL
        $sql = "SELECT $tablename.*, creator.name AS created_by_name, updator.name AS updated_by_name FROM $tablename".
               " LEFT JOIN $tablename_user AS creator ON $tablename.created_by_id = creator.id".
               " LEFT JOIN $tablename_user AS updator ON $tablename.updated_by_id = updator.id".
               " $where_string $order_by_string $limit_string";

        $stmt = self::$__CONN__->prepare($sql);
        $stmt->execute();

        // Run!
        if ($limit == 1) {
            return $stmt->fetchObject('Page');
        } else {
            $objects = array();
            while ($object = $stmt->fetchObject('Page')) {
                $objects[] = $object;
            }
            return $objects;
        }
    
    } // find
    
    public static function findAll($args = null)
    {
        return self::find($args);
    }
    
    public static function findById($id)
    {
        return self::find(array(
            'where' => self::tableNameFromClassName('Page').'.id='.(int)$id,
            'limit' => 1
        ));
    }

    public static function childrenOf($id)
    {
        return self::find(array('where' => 'parent_id='.$id, 'order' => 'position, created_on DESC'));
    }
    
    public static function hasChildren($id)
    {
        return (boolean) self::countFrom('Page', 'parent_id = '.(int)$id);
    }
    
    public static function replacePath($old, $new)
    {
        $sql = "UPDATE ".self::tableNameFromClassName('Page'). " SET slug = REPLACE(slug, '$old', '$new') WHERE slug LIKE ('$old%')";
        
        return self::$__CONN__->exec($sql);
    }
    
    public static function pathOf($id)
    {
        return self::findById($id)->slug;
    }
    
    public static function getSlug($slug)
    {
        // return the last part /en/about/service/[web]
        $pos = strrpos($slug, '/');
        return $pos !== false ? substr($slug, $pos+1): $slug;
    }
    
    public static function getPathOfSlug($slug)
    {
        // return the last part [/en/about/service]/web/
        return substr($slug, 0, strrpos($slug, '/'));
    }
    
} // end Page class