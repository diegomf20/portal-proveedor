<template>
    <v-container fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-6">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>PORTAL PROVEEDORES</v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <v-form autocomplete="off" v-on:submit.prevent="ingresar">
                  <!-- <select v-model="cuenta.empresa">
                    <option value="01">PROSERLA</option>
                    <option value="02">JAYANCA</option>
                  </select> -->
                  <v-row>
                    <v-col cols="12">
                      <v-text-field
                        label="Usuario"
                        name="usuario"
                        prepend-icon="mdi-account"
                        type="text"
                        v-model="cuenta.usuario"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        id="password"
                        label="Password"
                        name="password"
                        prepend-icon="mdi-lock"
                        type="password"
                        v-model="cuenta.password"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" class="text-right">
                        <v-btn type="submit" color="primary" :loading="espera">Ingresar</v-btn>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
          espera: false,
          cuenta: {
            usuario: '',
            password: '',
            empresa: '01'
          }
        }
    },
    methods: {
        ingresar(){
          this.espera=true;
            axios.post(url_base+'/login',this.cuenta)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "ERROR":
                        // this.$notify({
                        //     group: 'foo',
                        //     title: respuesta.data,
                        //     type: 'warn'
                        // })
                        swal({
                            title: respuesta.data,
                            icon: "error",
                        });
                        break;

                    case "OK":
                        this.$store.commit('auth_success', respuesta.data);
                        this.$router.push({path: "/"} );
                        break;

                    default:
                        break;
                }
                this.espera=false;
            });
        }
    },
}
</script>