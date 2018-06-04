@echo off
echo Apply3���ص��԰���
echo ����ָ�����£�
echo ɾ���������ݱ� php think apply3:drop
echo ����������ݱ� php think apply3:clear
echo �Զ��������ݱ� php think migrate:run
echo ��json�������ݣ� php think apply3:import *
echo ����place.json�� php think apply3:export place APP/public/static/place.json
echo ����resource.json�� php think apply3:export resource APP/public/static/resource.json
echo һ���ϳ����ڵ����룺 php think apply3:disable
echo �˳������д��ڣ� exit
cmd