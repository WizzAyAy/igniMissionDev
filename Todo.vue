<template>
<div>
    <!--
      v card qui gerer l'affichage des items
      on va boucler sur le tableau items pour pouvoir créer une v list 
      chaque item a deux v btn qui gere la modification et la suppression de son contenu

      le v-subheader dispose lui d'un bouton pour la création d'un nouvelle élement (non demandé)
    -->
    <v-card
        class="mx-auto"
        max-width="1000"
        width="750"
        >
        <v-list>
            <v-subheader>
                {{header}}
                <v-spacer/> 
                <v-btn elevation="0" small rounded @click.stop="dialogNewItem = true" icon>
                <v-img :src=add></v-img>
                </v-btn> 
            </v-subheader>
            
            
            <v-divider></v-divider>
        
            <v-list-item
            v-for="(item,index) in items" :key="index"
            >

            <v-list-item-title v-text="item.message"></v-list-item-title>
                
                <v-btn elevation="0" small rounded color="primary" @click.stop="renameItemSaveIndex(index)" icon>
                    <v-img :src=modfify></v-img>
                </v-btn> 
                <v-btn elevation="0" small rounded color="error" @click.stop="deleteItem(index)" icon>
                    <v-img :src=supp></v-img>
                </v-btn>
                
            </v-list-item>  

        </v-list>
        
    </v-card>

    <!--
      v dialog qui gerer la creation des items
      à noter qu'on aurait pu faire un autre composant vue,
      mais pour ce petit exemple cela ne me paraissait non necessaire
    -->

    <v-dialog
      v-model="dialogNewItem"
      max-width="450"
    >
      <v-card>
        <v-card-title>
          Ajouter un item à la Todo Liste
        </v-card-title>

         <v-col>
          <v-text-field
            v-model="textNewItem"
            label="Nom de la nouvelle tâche"
            outlined
            clearable
          ></v-text-field>
        </v-col>
          <v-spacer></v-spacer>

          <v-btn
            color="red darken-1"
            text
            @click="dialogNewItem = false"
          >
            Annuler
          </v-btn>
          <v-btn
            color="green darken-1"
            text
            @click="addItem()"
          >
            Ajouter
          </v-btn>
      </v-card>
    </v-dialog>

    <!--
      v dialog qui gerer le renomages des items
    -->

    <v-dialog
      v-model="dialogChangeItem"
      max-width="450"
    >
      <v-card>
        <v-card-title>
          Quel est le nouveau nom de cette tache ?
        </v-card-title>

         <v-col>
          <v-text-field
            v-model="textChangeItem"
            label="Nom de la tâche"
            outlined
            clearable
          ></v-text-field>
        </v-col>
          <v-spacer></v-spacer>

          <v-btn
            color="red darken-1"
            text
            @click="dialogChangeItem = false"
          >
            Annuler
          </v-btn>
          <v-btn
            color="green darken-1"
            text
            @click="renameItem()"
          >
            Valider
          </v-btn>
      </v-card>
    </v-dialog>

</div>
</template>

<script>

export default {
  name: 'Todo',


/*
  composant utilisant vuetify
  ce composant permet l'affichage d'une todolist
  si vous voulez afficher votre propre liste vous avez simplement besoin de changer la variables items 
  par exemple on pourrait prendre une varriable qui vient du store et qui quand un utilisateur se connect
  fait une request a une base de donées pour recuper la liste d'un utilisateur precis

  on pourra changer les methodes pour appeller le store au lieu de les changers juste dans l'array items
*/
  data () {
      return{
        //boolean qui represente l'etat v-dialog : ouvert ou fermé.
            dialogNewItem: false,
            dialogChangeItem: false,
        //int qui represente l'index de l'item en cours de renommage.
            ChangeTaskIndex: 0,
        //String qui represente la chaine de carractere du v-text-field 
            textNewItem: '',
            textChangeItem: '',
        //String qui represente le titre de la v list 
            header : "Todo List",
        //array pour tous les items de la todo list
            items: [
                { message : 'Do the dishes'},
                { message : 'Take out the trash'},
                { message : 'Finish doing laundry'}
            ],
        //image des differents boutons utiliser les icon mdi aurait pu etre une meilleur idée mais j'ai choisit celle ci car
        //j'ai eu un probleme avec mdi (icon invisible)
            add : require('../assets/add-black-18dp.svg'),
            modfify : require('../assets/create-black-18dp.svg'),
            supp : require('../assets/delete-black-18dp.svg'),
                
        }
    },


    methods: {
        //methode qui prend un int en entré et supprime de l'array l'item en position index
        deleteItem: function(index) {
            this.items.splice(index,1);
        },

        //methode qui sauvegarde un index dans ChangeTaskIndex et affiche une boite de dialog
        renameItemSaveIndex : function(index) {
            this.dialogChangeItem = true;
            this.ChangeTaskIndex = index;
        },

        //methode qui modifi le text d'un item de la todolist en position ChangeTaskIndex
        renameItem: function() {
            if(this.textChangeItem != "" && this.textChangeItem != null){
                this.items[this.ChangeTaskIndex].message= this.textChangeItem;
                this.dialogChangeItem = false;
                this.textChangeItem = "";
            }
        },

        //methode qui ajoute a la fin de l'array items un item avec le message prensent dans le v-text-field
        addItem: function() {
            if(this.textNewItem != "" && this.textNewItem != null){
                this.items.push({message : this.textNewItem});
                this.dialogNewItem = false;
                this.textNewItem = "";
            }
        }
    }
}
</script>
