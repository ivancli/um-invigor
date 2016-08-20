<?php echo '<?php' ?>

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class InitialSeeder extends Seeder
{

    public function run()
    {
        $userId = DB::table('{{$usersTable}}')->insertGetId([
                'email' => '{{'admin@um.dev'}}',
                'password' => bcrypt('secret'),
        ]);

        $roleId = DB::table('{{$rolesTable}}')->insertGetId([
                'name' => 'super_admin',
        ]);

        DB::table('{{$roleUserTable}}')->insert([
                'user_id' => $userId,
                'role_id' => $roleId,
        ]);

    }
}