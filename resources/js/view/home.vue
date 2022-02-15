<template>
    <container>
        <v-btn @click="prueba">a</v-btn>
    </container>
</template>
<script>
export default {
    methods:{
        prueba(){
            axios({
                method:'get',
                url: 'http://190.116.184.195:9083/api/SeguimientoDocumentario/status?empresa=01&ruc=20117332714&serie=F003&numero=0002056',
                // data: {
                //     id:'http://190.116.184.195:9083/api/SeguimientoDocumentario/status?empresa=01&ruc=00000000001&serie=F001&numero=00001'
                // }
            })
        }
    }
}
</script>