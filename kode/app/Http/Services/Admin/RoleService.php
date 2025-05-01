<?php

namespace App\Http\Services\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class RoleService extends Controller
{


    /**
     * Get all roles
     *
     * @return Collection
     */
    public function index() :Collection {
        return Role::with(['createdBy'])->latest()->get();
    }


    /**
     * Store a role
     *
     * @param Request $request
     * @return array
     */
    public function store(Request $request) :array
    {
        $role = Role::create([
            'name'        => $request->name,
            'slug'        => make_slug( $request->name),
            'created_by'  => auth_user()->id,
            'permissions' => json_encode($this->formatPermission($request)),
            'status'      => (StatusEnum::true)->status()
        ]);

        $response['status']  = "success";
        $response['message'] = translate('Role Created Succesfully');
        $response['data']    = $role;

        return  $response;
    }


    /**
     * Format permissions
     *
     * @param Request $request
     * @return array
     */
    public function formatPermission(Request $request) :array{
        $permissions = [];
        if($request->permission){
            foreach($request->permission as $key=>$permission){
                $permissions[][$key] = array_keys($permission);
            }
        }

        return $permissions;
    }


    /**
     * Update a specific role
     *
     * @param Request $request
     * @return array
     */
    public function update(Request $request) :array
    {
        $role = $this->role($request->id);

        $role = $role->update([
            'name'        => $request->name,
            'slug'        => make_slug( $request->name),
            'permissions' => json_encode($this->formatPermission($request)),
        ]);
        $response['status']  = "success";
        $response['message'] = translate('Roles Updated Succesfully');
        $response['data']    = $role;

        return $response;
    }



    /**
     * Get a specific role
     *
     * @param int | string $id
     *
     * @return Role
     */
    public function role(int | string $id) :Role{
       return Role::where('id',$id)->first();
    }


    /**
     * Destroy a specific role
     *
     * @param int | string $id
     *
     * @return array
     */
    public function destory(int | string $id) :array
    {
        $response['status']  = 'success';
        $response['message'] = translate('Deleted Successfully');
        try {
            $role = $this->role($id);
            $role->delete();

        } catch (\Throwable $th) {
            $response['status']  = 'error';
            $response['message'] = translate('Post Data Error !! Can Not Be Deleted');
        }
        return $response;
    }


    /**
     * Get all permissions
     *
     * @return array
     */
    public  function permissions() :array {

        $permissions = [
            [
                'admin' => [
                    'view_admin',
                    'update_profile',
                    'create_admin',
                    'update_admin',
                    'delete_admin',
                ]
            ],
            [
                'language' => [
                    'view_languages',
                    'create_languages',
                    'update_languages',
                    'delete_languages',
                ]
            ],
            [
                'role' => [
                    'view_roles',
                    'create_roles',
                    'update_roles',
                    'delete_roles',
                ]
            ],


            [
                'dashboard' => [
                    'view_dashboard',
                ]
            ],
            [
                'order' => [
                    'view_order',
                    'update_order',
                    'delete_order',
                ]
            ],

            [
                'configuration' => [
                    'view_configuration',
                    'update_configuration',
                    'delete_configuration',
                ]
            ],

            [
                'settings' => [
                    'view_settings',
                    'update_settings',
                    'create_settings',
                ]
            ],

            [
                'support' => [
                    'view_support',
                    'update_support',
                    'create_support',
                    'delete_support',

                ]
            ],
            [
                'Payment System' => [
                    'view_method',
                    'update_method',
                    'create_method',
                    'delete_method',

                ]
            ],
            [
                'brand' => [
                    'view_brand',
                    'update_brand',
                    'create_brand',
                    'delete_brand',

                ]
            ],
            [
                'category' => [
                    'view_category',
                    'update_category',
                    'create_category',
                    'delete_category',

                ]
            ],
            [
                'product' => [
                    'view_product',
                    'update_product',
                    'create_product',
                    'delete_product',

                ]
            ],
            [
                'promote' => [
                    'manage_deal',
                    'manage_offer',
                    'manage_cuppon',
                    'manage_campaign',

                ]
            ],
            [
                'log' => [
                    'view_log',
                    'update_log',
                    'delete_log'
                ]
            ],
            [
                'blog' => [
                    'manage_blog',
                ]
            ],
            [
                'seller' => [
                    'view_seller',
                    'update_seller',
                    'delete_seller',
                ]
            ],


            [
                'customer' => [
                    'manage_customer',
                ]
            ],


            [
                'delivery_man' => [
                    'manage_delivery_man',
                ]
            ],


            [
                'frontend' => [
                    'manage_frontend',
                ]
            ],

            [
                'countries' => [
                    'manage_countries',
                ]
            ],
            [
                'states' => [
                    'manage_states',
                ]
            ],
            [
                'cities' => [
                    'manage_cities',
                ]
            ],
            [
                'zones' => [
                    'manage_zones',
                ]
            ],
            [
                'tax' => [
                    'manage_taxes',
                ]
            ],






        ];
        return $permissions;
    }


}
