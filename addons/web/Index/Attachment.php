<?php namespace Aike\Web\Index;

use DB;
use Input;
use Auth;

class Attachment
{
    /**
     * 保存附件
     */
    public static function save($data)
    {
        $key = self::key();
        $id  = self::index($key);
        if ($id) {
            $data['id'] = $id;
            return DB::table('attachment_'.$key)->insertGetId($data);
        }
        return 0;
    }

    public static function files($name, $path = 'default')
    {
        $files = Input::file($name);
    
        $path = $path.'/'.date('Y/m');
        $upload_path = upload_path().'/'.$path;

        $res = [];

        foreach ($files as $file) {
            if ($file->isValid()) {
                // 文件后缀名
                $extension = $file->getClientOriginalExtension();
                // 兼容do客户端上传
                if ($extension == 'do') {
                    $clientName = $file->getClientOriginalName();
                    $extension = pathinfo(substr($clientName, 0, -3), PATHINFO_EXTENSION);
                }

                // 文件新名字
                $name = date('dhis_').str_random(4).'.'.$extension;
                $name = mb_strtolower($name);

                if ($file->move($upload_path, $name)) {
                    $res[] = Attachment::save([
                        'name'   => $name,
                        'path'   => $path.'/'.$name,
                        'type'   => $extension,
                        'size'   => $file->getClientSize(),
                        'status' => 1,
                    ]);
                }
            }
        }
        return join(',', array_filter($res));
    }

    public static function base64($images, $path = 'default', $extension = 'jpg')
    {
        $path = $path.date('/Y/m');
        $directory = public_path().'/uploads/'.$path;

        if (!is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }
        
        $res = [];

        foreach ($images as $image) {
            $name = date('dhis_').str_random(4).'.'.$extension;
            $name = mb_strtolower($name);

            $image = base64_decode(str_replace(' ', '+', $image));
            $size = file_put_contents($directory.'/'.$name, $image);
            if ($size) {
                $res[] = Attachment::save([
                    'name'   => $name,
                    'path'   => $path.'/'.$name,
                    'type'   => $extension,
                    'size'   => $size,
                    'status' => 1,
                ]);
            }
        }
        return join(',', array_filter($res));
    }

    /**
     * 获取索引编号
     */
    public static function index($table_id)
    {
        return DB::table('attachment_index')->insertGetId([
            'user_id'  => Auth::id(),
            'table_id' => $table_id,
        ]);
    }

    /**
     * 获取附件表编号
     */
    public static function key()
    {
        return Auth::id() % 10;
    }

    /**
     * 获取指定编号附件列表
     */
    public static function getAll($id, $status = null)
    {
        $id = array_filter(explode(',', $id));

        if (empty($id)) {
            return [];
        }

        $index = DB::table('attachment_index')
        ->whereIn('id', $id)
        ->get();

        if ($index->isEmpty()) {
            return [];
        }

        $table = [];
        foreach ($index as $key) {
            $table[$key['table_id']][] = $key['id'];
        }

        $res = [];
        foreach ($table as $key => $value) {
            $db = DB::table('attachment_'.$key)
            ->whereIn('id', $value)
            ->selectRaw('*, concat('.$key.') as table_id');

            if (is_numeric($status)) {
                $db->where('status', $status);
            }
            if ($res) {
                $res->unionAll($db);
            } else {
                $res = $db;
            }
        }
        return $res->get();
    }

    /**
     * 获取当前ID附件和草稿
     */
    public static function edit($id)
    {
        $res['main']  = self::getAll($id, 1);
        $res['draft'] = self::draft();
        return $res;
    }

    /**
     * 获取当前ID附件
     */
    public static function view($id)
    {
        $res['main'] = self::getAll($id, 1);
        return $res;
    }

    /**
     * 发布附件，改成状态为可用
     */
    public static function publish()
    {
        $rows = self::draft();

        if (empty($rows)) {
            return false;
        }

        $table = 'attachment_'.self::key();
        foreach ($rows as $row) {
            DB::table($table)->where('id', $row['id'])->update([
                'status' => 1,
            ]);
        }
        return true;
    }

    /**
     * 发布附件，改成状态为可用
     */
    public static function store()
    {
        $rows = self::draft();

        if ($rows->isEmpty()) {
            return false;
        }

        $table = 'attachment_'.self::key();
        foreach ($rows as $row) {
            DB::table($table)->where('id', $row['id'])->update([
                'status' => 1,
            ]);
        }
        return true;
    }

    /**
     * 获取草稿文件
     */
    public static function draft($user_id = 0)
    {
        if ($user_id == 0) {
            $user_id = Auth::id();
        }

        return DB::table('attachment_'.self::key())
        ->where('created_by', $user_id)
        ->where('status', '0')
        ->get();
    }

    /**
     * 删除附件和文件
     */
    public static function delete($id)
    {
        $rows = self::getAll($id);

        if (empty($rows)) {
            return 0;
        }

        foreach ($rows as $row) {
            // 删除文件
            $file = public_path().'/uploads/'.$row['path'];
            if (is_file($file)) {
                unlink($file);
            }
            DB::table('attachment_'.$row['table_id'])->where('id', $row['id'])->delete();
            DB::table('attachment_index')->where('id', $row['id'])->delete();
        }
        return 1;
    }
}
