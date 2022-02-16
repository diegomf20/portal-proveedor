<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Listado de Comprobantes</v-card-title>              
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm=4 lg="2">
                        <v-btn @click="open_nuevo=true" outlined color="info">Nuevo Comprobante</v-btn>
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
        <v-dialog v-model="open_nuevo" persistent max-width="350">
            <v-card>
                <v-card-title class="headline">Nuevo Comprobante</v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="12">
                            <v-select
                                prepend-icon="mdi-domain"
                                outlined
                                dense
                                label="Empresa:"
                                v-model="documento.empresa"
                                :items="empresas"
                                item-text="nombre_empresa"
                                item-value="id"
                                :hide-details='true'
                                :error-messages="error.empresa">
                            </v-select>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field
                                label="Serie:"
                                v-model="documento.serie"
                                type="text"
                                placeholder="F001"
                                :error-messages="error.serie"
                                >
                            </v-text-field>
                        </v-col>
                        <v-col cols="8">
                            <v-text-field
                                label="Número:"
                                v-model="documento.numero"
                                type="text"
                                :error-messages="error.numero"
                                >
                            </v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                label="Fecha Emisión:"
                                v-model="documento.fecha_emision"
                                type="date"
                                :error-messages="error.fecha_emision"
                                >
                            </v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-select
                                prepend-icon="mdi-domain"
                                outlined
                                dense
                                label="Moneda:"
                                v-model="documento.moneda"
                                :items="[
                                    {moneda: 'Soles'},
                                    {moneda: 'Dolares'}
                                ]"
                                item-text="moneda"
                                item-value="moneda"
                                :hide-details='true'
                                :error-messages="error.moneda">
                            </v-select>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                label="Monto:"
                                v-model="documento.monto"
                                type="text"
                                :error-messages="error.monto"
                                >
                            </v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-file-input
                                outlined
                                show-size
                                dense
                                truncate-length="30"
                                v-model="documento.file"
                                :error-messages="error.file"
                                accept="application/pdf"
                            ></v-file-input>
                        </v-col>
                        <div class="text-right mt-3">
                            <v-btn 
                                outlined 
                                color="secondary" 
                                @click="open_nuevo=false"
                                >Cancelar</v-btn>
                            <v-btn 
                                :loading="espera"
                                outlined 
                                color="primary" 
                                @click="guardar()"
                                >Guardar</v-btn>
                        </div>
                    </v-row>
                </v-card-text>
            </v-card>               
        </v-dialog>
    </v-container>    
</template>
<script>
import { mapState,mapMutations } from 'vuex'

export default {
    data() {
        return {
            espera: false,
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
        initForm(){
            return {
                empresa: '01',
                serie: '',
                numero: '',
                ruc: '',
                file: null,
                fecha_emision: '',
                moneda: 'Soles'
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
            formData.append('moneda', this.documento.moneda); 
            formData.append('monto', this.documento.monto); 

            this.espera=true;
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
                this.espera=false;
            });
        },
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
                // data=data.map(row => {
                //     const importe = Number(row.importe).toFixed(2);
                //     const importe_cta = Number(row.importe_cta).toFixed(2);
                //     return Object.assign({}, row, { importe,importe_cta })
                // })
                // data.map(row => {
                //     return Object.assign({}, row, { importe })
                // })
                this.table=data;
            });
        }
    }
}
</script>