<template>
    <component :is="layout">
        <router-view></router-view>
    </component>
</template>
<script>
var default_layouts="panel";

export default {
    computed:{
        layout(){
            console.log(this.$route.meta.layout);
            return (this.$route.meta.layout || default_layouts);
        }
    },
    mounted(){
        axios.get(url_base+`/documento/pendientes`)
        .then(response => {
            var data =response.data;
            for (let i = 0; i < data.length; i++) {
                const documento = data[i];
                
                axios(`http://190.116.184.195:9083/api/SeguimientoDocumentario/status?ruc=${documento.ruc}&empresa=${documento.empresa}&serie=${documento.serie}&numero=${documento.numero}`)
                .then(response => {
                    var status=response.data;
                    axios.post(url_base+`/documento/${documento.id}?_method=PUT`,{
                        fecha_recepcion: status.fecha_recepcion,
                        fecha_pago: status.fecha_pago
                    })
                    .then(response => {
                    });
                });
            }
        });
    }
}
</script>