#include <stdio.h>    
#include <stdlib.h>    
#include <string.h>   
#include <time.h>   
#include <unistd.h>   
#include <libmemcached/memcached.h>   

#define DEBUG_READ 1 

static memcached_st* get_conn() {
    memcached_st *memc;
    memcached_return rc;   
    memcached_server_st *servers;   

    //connect multi server    
    memc = memcached_create(NULL);   
    servers = memcached_server_list_append(NULL, (char*)"localhost", 11211, &rc);   

    rc = memcached_server_push(memc, servers);  
    memcached_server_free(servers);    

    //memcached_behavior_set(memc, MEMCACHED_BEHAVIOR_DISTRIBUTION, MEMCACHED_DISTRIBUTION_CONSISTENT);  
    memcached_behavior_set(memc, MEMCACHED_BEHAVIOR_RETRY_TIMEOUT, 30) ;  
    memcached_behavior_set(memc, MEMCACHED_BEHAVIOR_SERVER_FAILURE_LIMIT, 5) ;  
    memcached_behavior_set(memc, MEMCACHED_BEHAVIOR_REMOVE_FAILED_SERVERS, 1) ;  

    return memc;
}

int mem_set(const char* key, const char* value,time_t expiration) {
    if (NULL == key || NULL == value) {  
        return -1;
    }

    memcached_st *memc = get_conn();
    if(memc){
        uint32_t flags = 0;

        memcached_return rc;
        rc = memcached_set(memc, key, strlen(key),value, strlen(value), expiration, flags);

        memcached_free(memc);

        // insert ok
        if (MEMCACHED_SUCCESS == rc) {
            return 1;
        } else {
            return 0;
        }
    }else{
        return -1;
    }
}

const char* mem_get(const char* key) {
    if (NULL == key) {  
        return "no key";
    }
    memcached_st *memc = get_conn();

    if(memc){

        uint32_t flags = 0;

        memcached_return rc;

        size_t value_length;
        char* value = memcached_get(memc, key, strlen(key), &value_length, &flags, &rc);

        memcached_free(memc);
        // get ok
        if(MEMCACHED_SUCCESS == rc) {  
            return value;
        }
        return "no value";
    }else{
        return "no value";
    }
}

//gcc -lmemcached -g xxx.c -o xxx
//int main(int argc, char *argv[]) {   
//
//    const char *mem_key     = "testkey";
//    const char *mem_value   = "what a beautifule day!\r\nYes, it is.";
//
//#if DEBUG_READ    
//    int result = mem_set(mem_key, mem_value, 0);   
//    if(result) {
//        printf("insert key: %s, value: %s\n", mem_key, mem_value);
//    }
//#endif  
//
//    const char* get_value =  mem_get(mem_key);  
//    printf("get_value: %s\n", get_value);
//
//    return 0;   
//}
