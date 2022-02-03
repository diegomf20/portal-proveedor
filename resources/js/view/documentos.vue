<template>
    <v-card>
        <v-card-title>Seguimiento Documentario</v-card-title>              
        <v-card-text>
            <v-row>
                <v-col cols="12" lg="3">
                    <v-select
                        prepend-icon="mdi-domain"
                        outlined
                        dense
                        label="Empresa:"
                        v-model="documento.empresa"
                        :items="[
                            { 'id':'01' ,nombre_empresa:'PROSERLA'},
                            { 'id':'02' ,nombre_empresa:'JAYANCA'}
                        ]"
                        item-text="nombre_empresa"
                        item-value="id"
                        :hide-details='true'>
                    </v-select>
                </v-col>
                <v-col cols="12" sm=6 lg="1">
                    <v-text-field
                        label="Serie:"
                        v-model="documento.serie"
                        type="text"
                        >
                    </v-text-field>
                </v-col>
                <v-col cols="12" sm=6 lg="2">
                    <v-text-field
                        label="Número:"
                        v-model="documento.numero"
                        type="text"
                        >
                    </v-text-field>
                </v-col>
                <v-col cols="12" sm=4 lg="2">
                    <v-btn color="info" @click="consultar">
                        <i class="fas fa-search"></i> BUSCAR
                    </v-btn>
                </v-col>
                <v-col cols="12">
                    <v-data-table
                        color="red lighten-2"
                        class="elevation-1"
                        :headers="header"
                        :items="table"
                        >
                        <template v-slot:item.con_ccosto="{ item }">
                            <div v-if="item.con_ccosto=='No'">
                                <v-chip 
                                    :outlined='false'
                                    color="error" 
                                    @click="save(item.idrecepcion, item.item)" 
                                    small 
                                    v-if="cuenta.usuario=='GSEMINARIO'||cuenta.usuario=='ADMINISTRADOR'">
                                    Por Asignar
                                </v-chip>
                                <v-chip 
                                    :outlined='false'
                                    color="error" 
                                    small 
                                    v-else>
                                    Por Asignar
                                </v-chip>
                            </div>
                            <v-chip
                                v-else
                                small
                                class="ma-2"
                                color="success"
                                text-color="white"
                                >
                                {{ item.con_ccosto }}    
                                </v-chip>
                        </template>
                        <!-- con_ccosto -->
                    </v-data-table>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>

    
</template>
<script>
import { mapState,mapMutations } from 'vuex'

export default {
    data() {
        return {
            documento:{
                empresa: '01',
                serie: '',
                numero: '',
                ruc: '',
            },
            serie:'',
            numero: '',
            consulta: {
                ruc: '',
            },
            table: [],
            header: [
                // { text: 'Código', value: 'idclieprov' },
                // { text: 'Razón Social', value: 'razon_social' },
                { text: 'Fecha Registro', value: 'fecha_registro' },
                { text: 'Fecha Emisión', value: 'fecha_emision' },
                { text: 'Serie', value: 'serie' },
                { text: 'Número', value: 'numero'},
                // { text: 'Moneda', value: 'moneda'},
                // { text: 'Importe', value: 'importe',align: 'end' },
                // // { text: 'ID Recepción', value: 'idrecepcion' },
                // // { text: 'Item', value: 'item' },
                // { text: 'Recepción', value: 'fecha_recepcion' },
                // { text: 'C.Costo', value: 'con_ccosto' , align: 'center' },
                // { text: 'Provisión', value: 'fecha_provision' },
                { text: 'Empresa', value: 'empresa' },
                { text: 'PDF', value: 'ruta' },
                // { text: 'Fecha Pago', value: 'fecha_tesoreria' },
                // { text: 'Importe CTA', value: 'importe_cta' , align: 'end'}, //a la derecha y con 2 comas

            ],
            costo_asignado:{
                item: '',
                idrecepcion: '' 
            },



            descriptionLimit: 60,
            entries: [],
            isLoading: false,
            model: null,
            search: null,
        }
    },
    computed: {
        ...mapState(['cuenta','rutas']),
        // fields () {
        //     if (!this.consulta) return []

        //     return Object.keys(this.consulta).map(key => {
        //         return {
        //             key,
        //             value: this.consulta[key] || 'n/a',
        //         }
        //     })
        // },
        items () {
            return this.entries.map(entry => {
                const razon_social = entry.razon_social.length > this.razon_socialLimit
                    ? entry.razon_social.slice(0, this.razon_socialLimit) + '...'
                    : entry.razon_social
                return Object.assign({}, entry, { razon_social })
            })
        },
    },
    watch: {
      search (val) {
          
          // Items have already been loaded
        // if (this.consulta ==={
        //     idclieprov: '',
        //     razon_social: ''
        // }) return

        // // Items have already been requested
        if (this.isLoading) return
        
        if (val==''||val==null) {
            this.consulta={
                idclieprov: '',
                razon_social: ''
            }
        }else{
            this.isLoading = true
            // Lazily load input items
            console.log(val);
            fetch(url_base+`/cliente-proveedor?search=`+val+'&empresa='+this.cuenta.empresa)
              .then(res => res.json())
              .then(res => {
                //   console.log(res);
                  this.entries=res,
                  this.isLoading = false
                // const { count, entries } = res
                // this.count = count
                // this.entries = entries
              })
              .catch(err => {
                console.log(err)
              })
              .finally(() => (this.isLoading = false))
        }
        // }

      },
    },
    mounted(){
        this.consulta.ruc=this.cuenta.ruc;
        this.documento.ruc=this.cuenta.ruc;
        this.consultar();
    },
    methods: {
        buscarProveedor(){
            axios.get(url_base+`/cliente-proveedor/${this.consulta.idclieprov}`)
            .then(response => {
                this.consulta=response.data;
            });
        },
        consultar(){
            axios.get(url_base+`/documento`, {
                params: this.consulta
            })
            .then(response => {
                var data=response.data;
                data=data.map(row => {
                    const importe = Number(row.importe).toFixed(2);
                    const importe_cta = Number(row.importe_cta).toFixed(2);
                    return Object.assign({}, row, { importe,importe_cta })
                })
                // data.map(row => {
                //     return Object.assign({}, row, { importe })
                // })
                this.table=data;
            });
        },
        save(idrecepcion,item){
            var t=this;
            swal({ title: "¿Desea asignar C. Costo?", buttons: ['Cancelar',"Si"]})
            .then((res) => {
                if (res) {
                    axios.post(url_base+`/CostoAsignado`,{
                        empresa: t.cuenta.empresa,
                        idrecepcion: idrecepcion,
                        item: item

                    }).then(response => {
                        var res=response.data;
                        switch (res.status) {
                            case 'OK':
                                swal(res.message, { 
                                    icon: "success", 
                                    timer: 2000, 
                                    buttons: false
                                });
                                t.consultar();
                                break;
                        
                            default:
                                break;
                        }
                    });
                }
            });
        },
        changeEstado(){
            // axios.post(url_base+'/changueEstado?usuario='+this.cuenta.usuario)
            // .then(response => {
            // });
        }
    }
}
</script>