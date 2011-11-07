<?php
/**
 * Consider to create separate tables?
 * Should groups be stored as usermeta data?
 **/

class BU_Edit_Groups {

	public $option_name = '_bu_section_groups';
	public $groups = array();

	public function add(BU_Edit_Group $group) {
		array_push($this->groups, $group);
	}

	public function get($id) {
		if(isset($this->groups[$id])) {
			return $this->groups[$id];
		}
	}

	public function load() {
		$groups = get_option($this->option_name);
		if(is_array($groups)) $this->groups = $groups;
	}

	public function add_group($args) {
		$args['name'] = strip_tags(trim($args['name']));
		$args['description'] = strip_tags(trim($args['description']));

		$group = new BU_Edit_Group($args);
		$this->add($group);
	}

	public function update() {
		update_option($this->option_name, $this->groups);
	}

	public function update_group($id, $args = array()) {
		$group = $this->get($id);
		if($group) {
			$group->update_group($args);
		}
	}

	public function delete() {
		delete_option($this->option_name);
	}

	public function delete_group($id) {
		if($this->get($id)) {
			unset($this->groups[$id]);
		}
	}

	public function find_user($user_id) {
		$groups = array();
		foreach ($this->groups as $group) {
			if($group->has_user($user_id)) {
				array_push($groups, $group);
			}
		}
		return $groups;
	}

	public function delete_user($user_id) {
		$groups = $this->find_user($user_id);
		foreach($groups as $group) {
			$group->remove_user($user_id);
		}
	}

}

// class for listing groups (designed to be extended)


class BU_Groups_List {
	public $current_group;
	public $edit_groups;

	function __construct(BU_Edit_Groups $groups) {
		$this->edit_groups = $groups;
		$this->current_group = -1;
	}

	function have_groups() {
		if(count($this->edit_groups->groups) > 0 && $this->current_group < (count($this->edit_groups->groups) - 1)) {
			return true;
		} else {
			return false;
		}
	}

	function the_group() {
		$this->current_group++;
		return $this->edit_groups->get($this->current_group);
	}

	function rewind() {
		$this->current_group = -1;
	}

}


class BU_Edit_Group {
	public $users = array();
	public $description = '';
	public $name = null;

	function __construct($args = array()) {
		$this->name = $args['name'];
		$this->description = $args['description'];
		if(isset($args['users']) && is_array($args['users'])) {
			foreach($args['users'] as $user) {
				$this->add_user($user);
			}
		}
	}

	function has_user($user_id) {
		return in_array($user_id, $this->users);
	}

	function add_user($user_id) {
		// need to make sure the user is a member of the site
		if(!$this->has_user($user_id)) {
			array_push($this->users, $user_id);
		}
	}

	function remove_user($user_id) {
		if($this->have_user($user_id)) {
			unset($this->users[array_search($user_id, $this->users)]);
		}
	}

	function update($args = array()) {
		$current = array(
			'name' => $this->name,
			'description' => $this->description,
			'users' => $this->users
		);

		$updated = array_merge($current, $args);
	}


	function get_name() {
		return $this->name;
	}


	function get_description() {
		return $this->description;
	}
	/**
	 * Unfinished.
	 */
	function get_posts() {
		$query = new WP_Query();
	}

}

?>
