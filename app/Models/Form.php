<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;
use DB;

class Form extends Model
{
    protected $table = "form";
    public $timestamps = true;
    //protected $primaryKey = "form_id";

    /**
     * 获取需要审批的表单
     * @author Liangjianhua <github.com/Varsion>
     * @param [String] $work 工号
     * @param [int] $class   类别
     * @return array
     */
    public static function getApproFormList($work,$class) {
        try {
        $permi = \get_app_status($work);

        if($class != 0){
            $res = self::join('approval','form.form_id','approval.form_id')
                        ->join('user_info','form.work_id','user_info.user_id')
                        ->join('form_type as type','form.form_type_id','type.form_type_id')
                        ->select('form.form_id','user_info.name','type.form_type','form.created_at')
                        ->where('form.form_type_id',$class)
                        ->where('approval.status',$permi)
                        ->where('form.work_id','<>',$work)
                        ->paginate();
        } else {
            $res = self::join('approval','form.form_id','approval.form_id')
                        ->join('user_info','form.work_id','user_info.user_id')
                        ->join('form_type as type','form.form_type_id','type.form_type_id')
                        ->select('form.form_id','user_info.name','type.form_type','form.created_at')
                        ->where('approval.status',$permi)
                        ->where('form.work_id','<>',$work)
                        ->paginate();
        }

        return $res;
        } catch(Exception $e){
            logError('表单列表查询失败',[$e->getMessage()]);
        }
    }

    /**
     * 获取表单信息存放表
     * @author Liangjianhua <github.com/Varsion>
     * @param [String] $form_id
     * @return void
     */
    public static function getFrom_Type($form_id) {
        try {
            $res = self::select('form_type_id')
                        ->where('form_id',$form_id)
                        ->get();

            return $res[0]->form_type_id;

        } catch(Exception $e){
            logError('表单信息存放表查询失败',[$e->getMessage()]);
        }
    }

    /**
     * 模糊搜索搜索表单列表
     * @author Liangjianhua <github.com/Varsion>
     * @param [String] $value
     * @return void
     */
    public static function SearchForm($work,$value) {
        try {
            $permi = \get_app_status($work);

            $res = self::join('approval','form.form_id','approval.form_id')
                        ->join('user_info','form.work_id','user_info.user_id')
                        ->join('form_type as type','form.form_type_id','type.form_type_id')
                        ->select('form.form_id','user_info.name','type.form_type','form.created_at')
                        ->where('form.form_id',$value)
                        ->orWhere('form.form_id','like','%'.$value.'%')
                        ->where('user_info.name',$value)
                        ->orWhere('user_info.name','like','%'.$value.'%')
                        ->where('approval.status',$permi)
                        ->where('form.work_id','<>',$work)
                        ->get();

            return $res;

        } catch(Exception $e){
            logError('搜索表单失败，搜索参数为:'.$value,[$e->getMessage()]);
        }
    }

    /**
     * 模糊搜索失败表单列表
     * @author Liangjianhua <github.com/Varsion>
     * @param [String] $value
     * @return void
     */
    public static function SearchFormFail($work,$value) {
        try {

            $res = self::join('approval','form.form_id','approval.form_id')
                        ->join('user_info','form.work_id','user_info.user_id')
                        ->join('form_type as type','form.form_type_id','type.form_type_id')
                        ->select('form.form_id','user_info.name','type.form_type','form.created_at')
                        ->where('form.form_id',$value)
                        ->orWhere('form.form_id','like','%'.$value.'%')
                        ->whereIn('id',[2,4,6,8,10,12])
                        ->where('form.work_id',$work)
                        ->get();

            return $res;

        } catch(Exception $e){
            logError('搜索表单失败，搜索参数为:'.$value,[$e->getMessage()]);
        }
    }

    /**
     * 获取失败表单列表
     * @author Liangjianhua <github.com/Varsion>
     * @param [type] $class
     * @return void
     */
    public static function FailFormList($work,$class) {
        try {
            if(!$class){
                $res = self::join('approval','form.form_id','approval.form_id')
                            ->join('user_info','form.work_id','user_info.user_id')
                            ->join('form_type as type','form.form_type_id','type.form_type_id')
                            ->select('form.form_id','user_info.name','approval.status as approval_status','type.form_type','form.created_at')
                            ->where('form.work_id',$work)
                            ->whereIn('approval.status',[2,4,6,8,10,12])
                            ->paginate();
            } else {
                $res = self::join('approval','form.form_id','approval.form_id')
                            ->join('user_info','form.work_id','user_info.user_id')
                            ->join('form_type as type','form.form_type_id','type.form_type_id')
                            ->select('form.form_id','user_info.name','approval.status as approval_status','type.form_type','form.created_at')
                            ->where('form.form_type_id',$class)
                            ->where('form.work_id',$work)
                            ->whereIn('approval.status',[2,4,6,8,10,12])
                            ->paginate();
            }

            return $res;
            } catch(Exception $e){
                logError('错误表单列表查询失败',[$e->getMessage()]);
            }
    }

}
