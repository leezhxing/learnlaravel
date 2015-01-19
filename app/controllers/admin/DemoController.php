<?php

class Demo extends Eloquent{
	//属性
	protected $table        = 'my_demo'; 
	protected $primaryKey   = 'did';
	protected $connection   = 'logstat_rt';
	protected $timestamps   = 'false';
	protected $fillable     = ['firstname','lastname','email'];
	protected $guarded      = ['id','password'];
	protected $guarded      = ['*'];  //Eloquent 默认会防止 mass-assignment 。
	protected $incrementing = false; //通常 Eloquent 模型主键值会自动递增。但是您若想自定义主键，将 incrementing 属性设成 false 。


}