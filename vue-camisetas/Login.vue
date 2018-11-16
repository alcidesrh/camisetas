<template>
    <v-app>
        <v-content>
            <v-container>
                <v-layout justify-center>
                    <v-flex xs12 sm4>
                        <v-toolbar color="indigo" dark>
                            <v-toolbar-title>Login</v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-btn icon>
                                <v-icon>undo</v-icon>
                            </v-btn>
                        </v-toolbar>

                        <v-card>
                            <v-container
                                    fluid
                                    grid-list-md
                            >
                                <v-layout row wrap>
                                    <v-flex>
                                        <v-alert v-if="error"
                                                 :value="true"
                                                 type="error"
                                        >
                                            {{error}}
                                        </v-alert>
                                        <v-form method="post">
                                            <v-text-field name="username"
                                                          :rules="fieldRule"
                                                          label="Usuario"
                                                          :value="last_username"
                                                          required
                                            ></v-text-field>
                                            <v-text-field type="password" name="password"
                                                          :rules="fieldRule"
                                                          label="Contraseña"
                                                          required
                                            ></v-text-field>
                                            <v-layout row wrap>
                                                <v-flex style="text-align: center">
                                            <v-btn type="submit">Acceder</v-btn>
                                                </v-flex>
                                            </v-layout>
                                            <input type="hidden" name="_csrf_token"
                                                   :value="csrf_token">
                                        </v-form>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>
<script>
    export default {

        data: () => ({
            last_username: null,
            error: null,
            csrf_token: null,
            fieldRule: [
                v => !!v || 'Este campo es requerido'
            ]
        }),
        methods: {},
        computed: {},
        watch: {},
        created() {
            this.last_username = last_username;
            if (typeof errorMsg != typeof undefined)
                this.error = 'Usuario o contraseña incorrecto.';
            this.csrf_token = csrf_token
        }
    }
</script>