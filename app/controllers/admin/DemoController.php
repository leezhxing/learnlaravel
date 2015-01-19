<?php

class Demo extends Eloquent{
	//����
	protected $table        = 'my_demo'; 
	protected $primaryKey   = 'did';
	protected $connection   = 'logstat_rt';
	protected $timestamps   = 'false';
	protected $fillable     = ['firstname','lastname','email'];
	protected $guarded      = ['id','password'];
	protected $guarded      = ['*'];  //Eloquent Ĭ�ϻ��ֹ mass-assignment ��
	protected $incrementing = false; //ͨ�� Eloquent ģ������ֵ���Զ������������������Զ����������� incrementing ������� false ��


}