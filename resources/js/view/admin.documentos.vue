<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Listado de Comprobantes</v-card-title>              
            <v-card-text>
                <v-row>

                    <v-col cols="12" sm=4 lg="5">
                        <v-select
                            prepend-icon="mdi-domain"
                            outlined
                            dense
                            label="Proveedor:"
                            v-model="consulta.ruc"
                            :items="proveedores"
                            item-text="razon_social"
                            item-value="ruc"
                            :hide-details='true'>
                        </v-select>
                    </v-col>
                    <v-col cols="12" sm=4 lg="2">

                        <v-btn @click="consultar" outlined color="info">Buscar</v-btn>
                    </v-col>
                    <v-col cols="12">
                        <v-data-table
                            color="red lighten-2"
                            class="elevation-1"
                            :headers="header"
                            :items="table"
                            >
                            <template v-slot:item.empresa="{ item }">
                                {{ (item.empresa=='01') ? 'Proserla': 'Jayanca Fruits'  }}
                            </template>
                            <template v-slot:item.ruta="{ item }">
                                <v-btn color="error"
                                        elevation="2"
                                        small 
                                        :outlined=false
                                        :href="`/files/${ item.ruta_archivo }`" 
                                        target="_blank">
                                    <i class="far fa-file-pdf"></i> Ver
                                </v-btn>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
    </v-container>    
</template>
<script>
import { mapState,mapMutations } from 'vuex'

export default {
    data() {
        return {
            documento: this.initForm(),
            error:{

            },
            empresas: [
                { id:'01' ,nombre_empresa:'PROSERLA'},
                { id:'02' ,nombre_empresa:'JAYANCA'}
            ],
            serie:'',
            numero: '',
            consulta: {
                ruc: '',
            },
            open_nuevo: false,
            table: [],
            header: [
                { text: 'Fecha Registro', value: 'fecha_registro' },
                { text: 'Razón Social', value: 'razon_social' },
                { text: 'Fecha Emisión', value: 'fecha_emision' },
                { text: 'Serie', value: 'serie' },
                { text: 'Número', value: 'numero'},
                { text: 'Moneda', value: 'moneda' },
                { text: 'Monto', value: 'monto' },
                { text: 'Empresa', value: 'empresa' },
                { text: 'Fecha Recepcion', value: 'fecha_recepcion' },
                { text: 'Fecha Pago', value: 'fecha_pago' },
                { text: 'PDF', value: 'ruta' }

            ],
            costo_asignado:{
                item: '',
                idrecepcion: '' 
            },
            proveedores:[],


            descriptionLimit: 60,
            entries: [],
            isLoading: false,
            model: null,
            search: null,
        }
    },
    computed: {
        ...mapState(['cuenta','rutas']),
        items () {
            return this.entries.map(entry => {
                const razon_social = entry.razon_social.length > this.razon_socialLimit
                    ? entry.razon_social.slice(0, this.razon_socialLimit) + '...'
                    : entry.razon_social
                return Object.assign({}, entry, { razon_social })
            })
        },
    },
    mounted(){
        this.listarProveedor();
        this.consultar();
    },
    methods: {
        initForm(){
            return {
                empresa: '01',
                serie: '',
                numero: '',
                ruc: '',
                file: null,
                fecha_emision: ''
            }
        },
        guardar(){
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }

            let formData = new FormData();
            if (this.documento.file!=null) {
                formData.append('file', this.documento.file);
            }
            formData.append('ruc', this.documento.ruc);
            formData.append('serie', this.documento.serie);
            formData.append('numero', this.documento.numero);
            formData.append('empresa', this.documento.empresa);
            formData.append('fecha_emision', this.documento.fecha_emision);


            axios.post(url_base+'/documento',formData, config)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("Comprobante Registrado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.documento=this.initForm();
                        this.consulta.ruc=this.cuenta.ruc;
                        this.documento.ruc=this.cuenta.ruc;
                        this.open_nuevo=false;
                        this.consultar();
                        break;
                    case 'VALIDATION':
                        this.error={}
                        this.error=respuesta.data;
                        break;
                    case 'ERROR':
                        break;
                    default:

                        break;
                }
            });
        },
        listarProveedor(){
            axios.get(url_base+`/user?all`)
            .then(response => {
                this.proveedores=response.data;
            });
        },
        consultar(){
            axios.get(url_base+`/documento?ruc=${this.consulta.ruc}`)
            .then(response => {
                var data=response.data;
                this.table=data;
            });
        }
    }
}
</script>