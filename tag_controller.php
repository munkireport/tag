<?php

/**
 * Tag_controller class
 *
 * @package munkireport
 * @author AvB
 **/
class Tag_controller extends Module_controller
{
    public function __construct()
    {
        $this->module_path = dirname(__FILE__) .'/';
        $this->view_path = $this->module_path . 'views/';
    }
    
    public function listing()
    {
        $data['page'] = '';
        $obj = new View();
        $obj->view('tag_listing', $data, $this->view_path);
    }
    

    /**
     * Create a Tag
     *
     **/
    public function save()
    {
        $out = array();

        // Sanitize
        $serial_number = post('serial_number');
        $tag = post('tag');
        if ($serial_number and $tag) {
            if (authorized_for_serial($serial_number)) {
                $tag = Tag_model::updateOrCreate(
                    [
                        'serial_number' => $serial_number,
                        'tag' => $tag,
                    ],
                    [
                        'user' => $_SESSION['user'],
                        'timestamp' => time(),
                    ]
                );

                $out = $tag;
            } else {
                $out['status'] = 'error';
                $out['msg'] = 'Not authorized for this serial';
            }
        } else {
            $out['status'] = 'error';
            $out['msg'] = 'Missing data';
        }

        $obj = new View();
        $obj->view('json', array('msg' => $out));
    }

    /**
     * Retrieve data in json format
     *
     **/
    public function retrieve($serial_number = '')
    {
        $out = array();

        $Tag = Tag_model::where('tag.serial_number', $serial_number)
            ->filter()
            ->get();

        $obj = new View();
        $obj->view('json', array('msg' => $Tag->toArray()));
    }

    /**
     * Delete Tag
     *
     **/
    public function delete($serial_number = '', $id = -1)
    {
        $out = [];
        $where = [];

        if (authorized_for_serial($serial_number)) {
            $where[] = ['serial_number', $serial_number];
            if($id){
                $where[] = ['id', $id];
            }
            Tag_model::where($where)
                ->delete();
            $out['status'] = 'success';
        }else{
            $out['status'] = 'error';
        }

        $obj = new View();
        $obj->view('json', array('msg' => $out));
    }
    
    /**
     * Get all defined tags
     *
     * Returns a JSON array with all defined tags, used for typeahead
     *
     **/
    public function all_tags($add_count = false)
    {
        $Tag = Tag_model::selectRaw('tag, count(*) as cnt')
            ->filter()
            ->groupBy('tag')
            ->orderBy('tag', 'asc');

        
            if ($add_count) {
                $out = $Tag->get()->toArray();
            } else {
                $out = $Tag->get()->pluck('tag')->toArray();
            }
        
        $obj = new View();
        $obj->view('json', ['msg' => $out]);
    }
} // END class Tag_controller
