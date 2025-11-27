<template>
  <v-card flat>
    <v-card-title class="d-flex justify-space-between align-center">
      <span class="text-h6">Loans</span>
      <v-btn size="small" variant="tonal" :loading="loading" @click="loadLoans">
        Refresh
      </v-btn>
    </v-card-title>

    <v-data-table
      class="elevation-1"
      :headers="headers"
      :items-per-page-options="[25, 50, 100]"
      :items-per-page="25"
      :items="loans"
      :loading="loading"
    >
      <template #item.loaned_at="{item}">
        {{ moment(item.loaned_at).format('MMM Do YYYY \\a\\t h:mm A') }}
      </template>

      <template #item.due_at="{item}">
        {{ moment(item.due_at).format('MMM Do YYYY \\a\\t h:mm A') }}
      </template>

      <template #item.returned_at="{item}">
        {{ item.returned_at ? moment(item.returned_at).format('MMM Do YYYY \\a\\t h:mm A') : '-' }}
      </template>


      <template #loading>
        <v-sheet class="pa-4 text-center">Loading loans...</v-sheet>
      </template>

      <!-- Extend Button Column -->
      <template #item.actions="{ item }">
        <v-btn
          icon="mdi-calendar-plus"
          variant="text"
          :disabled="item.returned_at !== null"
          @click="openExtendDialog(item)"
        />
      </template>

    
    </v-data-table>

    
    <!-- Extend Loan Dialog -->
    <v-dialog v-model="extendDialog" persistent max-width="400px">
      <v-card>
        <v-card-title>
          Extend Loan
        </v-card-title>

        <v-card-text>
          <div class="mb-3">
            <v-select
              label="Add Days"
              :items="dayOptions"
              v-model="extendDays"
            />
          </div>

          <!-- Live preview of new due date -->
          <v-chip v-if="selectedLoan" color="blue" class="mt-2">
            New Due Date:
            {{ newDueDate }}
          </v-chip>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn text @click="extendDialog = false">Cancel</v-btn>
          <v-btn color="primary" @click="submitExtend">Submit</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-card>
</template>

<script>
import { toast } from 'vue3-toastify';
import axios from 'axios';
import moment from 'moment';

export default {
  name: 'LoansTab',

  data () {
    return {
      moment,

      loading: false,
      loans: [],
      headers: [
        { title: 'ID', key: 'id' },
        { title: 'User', key: 'user.name' },
        { title: 'Book', key: 'book.title' },
        { title: 'Loan Date', key: 'loaned_at' },
        { title: 'Loan Due', key: 'due_at' },
        { title: 'Return Date', key: 'returned_at' },
      ],
    };
  },

  computed: {
    newDueDate() {
      if (!this.selectedLoan) return "";
      return moment(this.selectedLoan.due_at)
        .add(this.extendDays, "days")
        .format("MMM Do YYYY h:mm A");
    },
  },

  methods: {
    loadLoans () {
      this.loading = true;

      return axios.get('/api/v1/loans')
        .then(r => this.loans = r.data)
        .catch(e => {
          toast(e.response?.data?.message || e.response?.statusText || 'Error', {type: 'error'});
          console.error(e);
        })
        .finally(() => this.loading = false);
    },

    openExtendDialog(loan) {
      this.selectedLoan = loan;
      this.extendDays = 7;
      this.extendDialog = true;
    },

    submitExtend() {
      axios
        .put(`/api/v1/loans/extend/${this.selectedLoan.id}`, {
          additional_days: this.extendDays,
        })
        .then((r) => {
          toast("Loan extended successfully!", { type: "success" });
          this.extendDialog = false;
          this.loadLoans();
        })
        .catch((e) => {
          toast(e.response?.data?.message || "Error extending loan", {
            type: "error",
          });
          console.error(e);
        });
    },

  },

  mounted () {
    this.loadLoans();
  },
};
</script>
