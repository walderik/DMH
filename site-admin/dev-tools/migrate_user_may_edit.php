<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require $root . '/includes/init.php';

$roles = Role::all();

foreach ($roles as $role) {
    $larp_roles = activeRegistrationsMayNotEdit($role);
    if (empty($larp_roles)) $role->UserMayEdit = 1;
    else $role->UserMayEdit = 0;
    $role->update();
}
echo "Done";
exit;





function activeRegistrationsMayNotEdit(Role $role) {
    $sql = "SELECT regsys_larp_role.* FROM regsys_larp_role, regsys_larp WHERE RoleId = ? and UserMayEdit = 0 AND regsys_larp_role.LarpId = regsys_larp.Id AND regsys_larp.EndDate >= NOW()";
    return LARP_Role::getSeveralObjectsqQuery($sql, array($role->Id));
}