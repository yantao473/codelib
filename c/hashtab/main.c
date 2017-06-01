#include "xdict.h"

int main(){
	int i;

	char* names[]={"name","address","phone","k101","k110"};
	char* values[]={"Sourav","Sinagor","26300788","Value1","Value2"};

	inithashtab();

	for(i = 0; i<5; i++){
		install(names[i],values[i]);
    }

//	printf("Done\n");
//	printf("If we did not do anything wrong.. we should see %s\n",get("k110"));
//
//	install("phone","9433120451");
//
//	printf("Again if we go right, we have %s and %s\n",get("k101"),get("phone"));
//
	displaytable();
	cleanup();
	return 0;
}
