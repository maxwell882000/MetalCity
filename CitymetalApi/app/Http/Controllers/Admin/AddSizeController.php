<?php

namespace App\Http\Controllers\Admin;

use App\HandbookCategory;
use App\Http\Controllers\Controller;
use App\Repositories\HandbookCategoryRepositoryInterface;
use Illuminate\Http\Request;

class AddSizeController extends Controller
{
    /**
     * HandbookCategory repository
     *
     * @var HandbookCategoryRepositoryInterface
     */
    protected $handbookCategoryRepository;

    /**
     * Create a new instance
     *
     * @param HandbookCategoryRepositoryInterface $handbookCategoryRepository
     * @return void
     */
    public function __construct(HandbookCategoryRepositoryInterface $handbookCategoryRepository)
    {
        $this->handbookCategoryRepository = $handbookCategoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'categories' => $this->handbookCategoryRepository->all()
        ];

        return view('admin.add-size.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->route('admin.add-size.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'ru_title' => 'required|max:255',
        ]);
        $parent = $request->input('parent');
        $list = $this->handbookCategoryRepository->get($parent);
        foreach ($list->categories()->get() as $country) {
            $size = HandbookCategory::create($request->all());
            $size->parent_id = $country->id;
            $size->save();
        }
        if (!$request->has('saveQuit')) {
            return redirect()->route('admin.add-size.show', $parent);
        }
        return redirect()->route('admin.add-size.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('admin.add-size.create', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.add-size.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('admin.add-size.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->route('admin.add-size.index');
    }


}
