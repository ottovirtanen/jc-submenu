<?php
class SplitMenuTest extends WP_UnitTestCase{

	public function setUp(){
        parent::setUp();
        $this->submenu = $GLOBALS['jcsubmenu'];
    }

    public function testSplitMenu01(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'trigger_depth' => 0, 'menu_start' => 1, 'menu_depth' => 1, 'show_parent' => false));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);
        unset($elements[6]);
        unset($elements[5]);
        unset($elements[4]);
        unset($elements[1]);
        $this->assertEquals($elements, $output);

    }

    public function testSplitMenu02(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
                'current' => 1
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'trigger_depth' => 0, 'menu_start' => 1, 'menu_depth' => 1, 'show_parent' => false));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);
        unset($elements[3]);
        unset($elements[2]);
        unset($elements[0]);
        $this->assertEquals($elements, $output);

    }

    public function testSplitMenu03(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
                
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'trigger_depth' => 0, 'menu_start' => 1, 'menu_depth' => 1, 'show_parent' => false));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);
        unset($elements[6]);
        unset($elements[5]);
        unset($elements[4]);
        unset($elements[1]);
        $this->assertEquals($elements, $output);

    }

    public function testSplitMenu04(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
                
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'trigger_depth' => 0, 'menu_start' => 1, 'menu_depth' => 1, 'show_parent' => false));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);
        unset($elements[3]);
        unset($elements[2]);
        unset($elements[0]);
        $this->assertEquals($elements, $output);

    }

    public function testSplitMenu05(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'trigger_depth' => 0, 'menu_start' => 1, 'menu_depth' => 1, 'show_parent' => false));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);
        $this->assertEmpty($output);

    }
}

?>