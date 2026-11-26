<template>
    <el-dialog :title="titleDialog"
               width="65%"
               :visible="showDialog"
               @open="create"
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               append-to-body
               :show-close="false">

        <div class="form-body" v-if="persons">
            <div class="row" >
                <div class="col-lg-12 col-md-12">
                    <table width="100%">
                        <el-button v-if="loading_search" type="primary" slot="append" :loading="loading_search" icon="el-icon-search">
                        </el-button>
                        <thead>
                            <tr width="100%">
                                <th width="17%">Tipo de documento</th>
                                <th>N° documento</th>
                                <th>Apellidos y nombres</th>
                                <th width="15%">Tipo</th>
                                <th width="15%"><a href="#" @click.prevent="clickAddPerson" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in persons" :key="index" width="100%" >
                                <td>
                                    <div class="form-group mb-2 me-2">
                                        <el-select v-model="row.identity_document_type_id" placeholder="Tipo">
                                            <el-option v-for="d in documentTypes" :key="d.id" :label="d.label" :value="d.id"></el-option>
                                        </el-select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-2 me-2"  >
                                        <el-input @change="searchPerson(row,index)" @blur="duplicateDocument(row.number, index)" v-model="row.number" placeholder="Número de documento"></el-input>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-2 me-2" >
                                        <el-input v-model="row.name" placeholder="Nombre completo"></el-input>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-2 me-2" >
                                        <el-select v-model="row.type" placeholder="Seleccionar tipo">
                                            <el-option label="Adulto" value="adult"></el-option>
                                            <el-option label="Niño" value="child"></el-option>
                                        </el-select>
                                    </div>
                                </td>
                                <td class="series-table-actions text-center">
                                    <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancel(index)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                                <br>
                            </tr>
                        </tbody>
                    </table>


                </div>

            </div>
        </div>

        <div class="form-actions text-end pt-2">
            <el-button class="me-2" @click.prevent="clickCancelSubmit()">Cancelar</el-button>
            <el-button type="primary" @click="submit" >Guardar</el-button>
        </div>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'persons', 'quantity'],
        data() {
            return {
                titleDialog: 'Personas en la habitación',
                // Mismos códigos SUNAT que usa el cliente (identity_document_type_id).
                documentTypes: [
                    { id: '1', label: 'DNI' },
                    { id: '7', label: 'Pasaporte' },
                    { id: '4', label: 'Carnet de extranjería' },
                    { id: '6', label: 'RUC' },
                    { id: '0', label: 'Otro' },
                ],
                loading: false,
                errors: {},
                form: {},
                loading_search: false,
            }
        },
        async created() {

        },
        watch: {
            persons: {
                handler: function(newVal) {
                    this.calculatePersonCounts();
                },
                deep: true
            }
        },
        methods: {
            async duplicateDocument(data, index)
            {

                let duplicates = await _.filter(this.persons, {'number':data})
                if(duplicates.length > 1)
                {
                    this.persons[index].number = ''
                }
            },
            create(){
                if(this.persons.length == 0){
                    this.clickAddPerson()
                }
                // Registros antiguos: corregir nombre/documento invertidos y
                // deducir el tipo por el número.
                this.persons.forEach(p => {
                    this.fixSwapped(p);
                    if (!p.identity_document_type_id) {
                        this.$set(p, 'identity_document_type_id', this.guessDocType(p.number));
                    }
                });
            },
            // Si escribieron el nombre en la columna del documento y el
            // documento en la del nombre, se invierten solos.
            fixSwapped(p) {
                const looksLikeDoc = s => /^[A-Za-z0-9-]{5,15}$/.test(s) && /\d/.test(s);
                const number = String(p.number || '').trim();
                const name = String(p.name || '').trim();
                if (looksLikeDoc(name) && number && !/\d/.test(number)) {
                    p.number = name;
                    p.name = number;
                }
            },
            guessDocType(number) {
                const n = String(number || '').trim();
                if (/^\d{8}$/.test(n)) return '1';
                if (/^\d{11}$/.test(n)) return '6';
                if (/[A-Za-z]/.test(n) && /\d/.test(n)) return '7';
                return n ? '0' : '1';
            },
            async validatePersons(){

                let error = 0

                await this.persons.forEach(element => {
                    if(element.number == null){
                        error++
                    }
                });

                if(error>0)
                    return {success:false, message:'El campo número es obligatorio'}

                return {success:true}

            },
            calculatePersonCounts() {
                const adults = this.persons.filter(p => p.type === 'adult').length;
                const children = this.persons.filter(p => p.type === 'child').length;
                
                this.$emit('updatePersonCounts', {
                    adults: adults,
                    children: children
                });
            },
            async submit(){

                let val_persons = await this.validatePersons()
                if(!val_persons.success)
                    return this.$message.error(val_persons.message);

                // Emitir una copia nueva del array para forzar reactividad en el
                // padre, evitando que Vue no detecte cambios cuando se mutan
                // elementos del array por referencia compartida.
                this.persons.forEach(p => {
                    this.fixSwapped(p);
                    if (!p.identity_document_type_id) {
                        this.$set(p, 'identity_document_type_id', this.guessDocType(p.number));
                    }
                });
                const personsCopy = this.persons.map(p => ({ ...p }));
                await this.$emit('addRowPerson', personsCopy);
                this.calculatePersonCounts();
                await this.$emit('update:showDialog', false)

            },
            clickAddPerson() {
                this.persons.push({
                    identity_document_type_id: '1',
                    number: null,
                    name:  '',
                    type: 'adult'
                });
                this.calculatePersonCounts();
            },

            close() {
                this.$emit('update:showDialog', false)
            },
            clickCancel(index) {
                this.persons.splice(index, 1);
                this.calculatePersonCounts();
            },
            async clickCancelSubmit() {
                await this.$emit('update:showDialog', false)
            },
            close() {
                this.$emit('update:showDialog', false)
            },
            searchPerson(row,index) {
                // Solo DNI y RUC se consultan en RENIEC/SUNAT.
                const docType = row.identity_document_type_id || this.guessDocType(row.number);
                if (docType === '1' && /^\d{8}$/.test(row.number || '')) {
                    this.searchServiceNumberByType('dni', row)
                } else if (docType === '6' && /^\d{11}$/.test(row.number || '')) {
                    this.searchServiceNumberByType('ruc', row)
                }
                
            },
            async searchServiceNumberByType(type,row) {
                this.loading_search = true
                let response = await this.$http.get(`/service/${type}/${row.number}`)
                if(response.data.success) {
                    let data = response.data.data
                    row.name = data.name

                } else {
                    this.$message.error(response.data.message)
                }
                this.loading_search = false
            },
        }
    }
</script>
