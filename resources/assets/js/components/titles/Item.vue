<template>
    <li class="table-row" :class="rowClass">
        <span class="table-column name">
            <input type="text" @change="save" v-model="item.name">
        </span>
        <span class="table-column name">
            <input type="text" @change="save" v-model="item.nameEnglish">
        </span>
        <span class="table-column holding">
            <input type="text" class="input-holding" @change="save" v-model="item.holding">
        </span>
        <span class="table-column winner" v-text="winnerName"></span>
        <span class="table-column order">
            <input type="text" class="input-sortorder" @change="save" v-model="item.sortOrder">
        </span>
        <span class="table-column team">
            <input type="checkbox" @change="save" v-model="item.isTeam">
        </span>
        <span class="table-column filename">
            <input type="text" @change="save" v-model="item.htmlFileName">
        </span>
        <span class="table-column modified">
            <input type="text" class="datepicker input-modified" @change="saveDatepicker($event)" v-model="item.htmlFileModified">
        </span>
        <span class="table-column closed">
            <input type="checkbox" @change="save" v-model="item.isClosed" :disabled="!isSaved()">
        </span>
        <span class="table-column open-detail">
            <a class="view-link" @click="select()" v-text="label"></a>
        </span>
    </li>
</template>

<script>
export default {
  props: {
    item: Object
  },
  data: () => {
    return {
      label: String
    };
  },
  methods: {
    save() {
      // 未登録なら何もしない
      if (!this.isSaved()) {
        return;
      }
      // 更新処理
      this.$http.put(`/api/titles/${this.item.id}`, this.item).catch(res => {
        const message = res.body.response.message;
        this.$store.dispatch("openDialog", {
          messages: message || "更新に失敗しました…。",
          type: "error"
        });
      });
    },
    select() {
      if (!this.isSaved()) {
        this.add();
      } else {
        this.$store.dispatch("openModal", {
          url: this.item.url
        });
      }
    },
    add() {
      // 登録処理
      this.$http
        .post("/api/titles/", this.item)
        .then(res => {
          this.item.id = res.body.response.id;
          this.setLabel();
          this.$store.dispatch("openDialog", {
            messages: `タイトル【${this.item.name}】を登録しました。`
          });
        })
        .catch(res => {
          const message = res.body.response.message;
          this.$store.dispatch("openDialog", {
            messages: message || "登録に失敗しました…。",
            type: "error"
          });
        });
    },
    saveDatepicker($event) {
      if (this.item.htmlFileModified !== $event.target.value) {
        this.item.htmlFileModified = $event.target.value;
        this.save();
      }
    },
    setLabel() {
      this.label = this.isSaved() ? "開く" : "登録";
    },
    isSaved() {
      return this.item.id !== null && this.item.id !== undefined;
    }
  },
  computed: {
    winnerName() {
      return this.item.winnerName || "";
    },
    rowClass() {
      return this.item.isClosed ? "table-row-closed" : "";
    }
  },
  mounted() {
    this.setLabel();
  }
};
</script>
