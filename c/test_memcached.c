//gcc -o test_memcached test_memcached.c -lmemcached

#include <stdio.h>
#include <string.h>
#include <libmemcached/memcached.h>

int main(int argc,char *argv[]) {
   //connect server

   memcached_st *memc;
   memcached_return rc;
   memcached_server_st *server;
   time_t expiration;
   uint32_t flags;

   memc = memcached_create(NULL);
   server = memcached_server_list_append(NULL,"localhost",11211,&rc);
   rc = memcached_server_push(memc, server);
   memcached_server_list_free(server);

   char *key = "key";
   char *value = "value";
   size_t vlen = strlen(value);
   size_t klen= strlen(key);


   //Save data

   rc = memcached_set(memc, key, klen, value, vlen, expiration, flags);
   if(rc == MEMCACHED_SUCCESS) { 
        printf("Save data: %s successful!\n", value);
   }

   //Get data
   char *result = memcached_get(memc, key, klen, &vlen, &flags, &rc);
   if(rc == MEMCACHED_SUCCESS) {
        printf("Get value: %s successful!\n", result);
   }

   //Delete data
   rc = memcached_delete(memc, key, klen, expiration);
   if(rc == MEMCACHED_SUCCESS) {
        printf("Delete key: %s successful!\n", key);
   }

   //free

   memcached_free(memc);
   return 0;
}
