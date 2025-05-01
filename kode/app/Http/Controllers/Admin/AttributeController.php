<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Str;

class AttributeController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permissions:view_product'])->only('index','getAttributeValue');
        $this->middleware(['permissions:create_product'])->only('store','attributeValueStore');
        $this->middleware(['permissions:update_product'])->only('update','attributeValueUpdate');
        $this->middleware(['permissions:delete_product'])->only('delete','attributeValueDelete','attributeDelete');

    }

    public function index()
    {
        $title = translate('All Attribute');
        $attributes = Attribute::latest()->with('value')->paginate(site_settings('pagination_number',10));
        $attributeForValues = Attribute::select('id', 'name')->orderBy('id', 'DESC')->get();
        return view('admin.attribute.index', compact('title', 'attributes', 'attributeForValues'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|max:255|unique:attributes,name',
            'status' => 'required|in:1,2',
        ]);
        Attribute::create($data);

        return back()->with('success', translate("Attribute has been created"));
    }

    public function update(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|max:255|unique:attributes,name,'.$request->id,
            'status' => 'required|in:1,2',
        ]);
        $attribute = Attribute::where('id', $request->id)->first();
        $attribute->update($data);
        return back()->with('success', translate("Attribute has been updated"));
    }

    public function attributeValueStore(Request $request)
    {
        $data = $this->validate($request, [
            'attribute_id' => 'required|exists:attributes,id',
            'name' => 'required|max:255|unique:attribute_values,name',
        ]);
        $name  = $data['name'];
        $data['name'] =  Str::slug( $name ,'-');
        $data['display_name'] =   $name ;

        AttributeValue::create($data);
        return back()->with('success', translate("Attribute value has been created"));
    }

    public function getAttributeValue($attributeId)
    {
        $attribute = Attribute::where('id',$attributeId)->first();
        $title = ucfirst($attribute->name)." Attribute value";
        $attributeValues = AttributeValue::where('attribute_id', $attributeId)->with('attribute')->paginate(site_settings('pagination_number',10));
        return view('admin.attribute.value', compact('title', 'attributeValues', 'attribute'));
    }

    public function attributeValueUpdate(Request $request)
    {

        $data = $this->validate($request, [
            'attribute_id' => 'required|exists:attributes,id',
            'id' => 'required|exists:attribute_values,id',
            'name' => 'required|max:255|unique:attribute_values,name,'.$request->id,
        ]);

        $attributeValue = AttributeValue::where('id',$request->id)->first();
        $name  = $data['name'];
        $data['name'] =  Str::slug(   $name ,'-');
        $data['display_name'] =  $name ;
        $attributeValue->update($data);

        return back()->with('success', translate("Attribute value has been updated"));
    }

    public function attributeValueDelete(Request $request)
    {
        $attributeValue = AttributeValue::where('id', $request->id)->first();
        $attributeValue->delete();
        return back()->with('success', translate("The Attribute value has been deleted"));
    }

    public function attributeDelete(Request $request)
    {
        $attribute = Attribute::with('value')->where('id', $request->id)->first();
        @$attribute->value()->delete();
        $attribute->delete();

        return back()->with('success', translate("The Attribute has been deleted"));
    }
}
