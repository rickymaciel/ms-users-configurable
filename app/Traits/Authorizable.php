<?php

namespace App\Traits;

class Authorizable {
  
  // Roles list
  const ADMIN = 'admin';

  // Permissions list
  const CREATE_USER = 'create_user';
  const EDIT_USER = 'edit_user';
  const MANAGE_ROLES = 'manage_roles';

}