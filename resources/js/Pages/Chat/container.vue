<template>
  <app-layout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Chat
        <chat-room-selection
          v-if="currentRoom.id"
          :rooms="chatRooms"
          :currentRoom="currentRoom"
          v-on:roomchanged="setRoom($event)"
        />
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <message-container :messages="messages" />
          <input-message :room="currentRoom" v-on:messagesent="getMessages()" />
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import MessageContainer from "./messageContainer.vue";
import InputMessage from "./inputMessage.vue";
import ChatRoomSelection from "./chatRoomSelection.vue";

export default {
  components: {
    AppLayout,
    MessageContainer,
    InputMessage,
    ChatRoomSelection,
  },
  data() {
    return {
      chatRooms: [],
      currentRoom: [],
      messages: [],
    };
  },
  watch: {
    currentRoom(val, oldVal) {
      if (oldVal.id) {
        this.disconnect(oldVal);
      }
      this.connect();
    },
  },
  methods: {
    connect() {
      console.log(this.currentRoom.id);
      if (this.currentRoom.id) {
        let vm = this;
        this.getMessages();
        window.Echo.private("chat." + this.currentRoom.id).listen(
          ".message.new",
          (e) => {
            vm.getMessages();
          }
        );
      }
    },
    disconnect(room) {
      window.Echo.leave("chat." + room.id);
    },
    getRooms() {
      axios
        .get("/chat/rooms")
        .then((result) => {
          this.chatRooms = result.data;
          var rooms = result.data;
          this.setRoom(rooms[0]);
        })
        .catch((err) => {
          console.log(err);
        });
    },
    setRoom(room) {
      this.currentRoom = room;
    },
    getMessages() {
      var currId = this.currentRoom.id;
      axios
        .get("/chat/room/" + currId + "/messages")
        .then((result) => {
          this.messages = result.data;
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
  created() {
    this.getRooms();
  },
};
</script>
