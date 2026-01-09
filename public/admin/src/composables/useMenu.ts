import { ref } from "vue";
import {
  CalenderIcon,
  UserCircleIcon,
  PieChartIcon,
  PageIcon,
  TableIcon,
  ListIcon,
  FolderIcon,
  UserGroupIcon,
  BarChartIcon,
  DocsIcon,
  HomeIcon,
} from "../icons";

// Icon mapping
const iconMap: Record<string, any> = {
  CalenderIcon,
  UserCircleIcon,
  PieChartIcon,
  PageIcon,
  TableIcon,
  ListIcon,
  FolderIcon,
  UserGroupIcon,
  BarChartIcon,
  DocsIcon,
  HomeIcon,
  // Default fallback
  GridIcon: PageIcon,
};

// Shared state (singleton pattern) - menu data cached across all components
const menuGroups = ref<any[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);
let isFetched = false; // Track if menu has been fetched

export function useMenu() {

  const fetchMenu = async () => {
    // Skip if already fetched
    if (isFetched) {
      return;
    }

    loading.value = true;
    error.value = null;

    try {
      const response = await fetch("/admin/api/menu", {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json",
        },
        credentials: "same-origin", // Include cookies for session auth
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();

      if (data.success && data.data) {
        // Map icon strings to actual icon components
        menuGroups.value = data.data.map((group: any) => ({
          ...group,
          items: group.items.map((item: any) => ({
            ...item,
            icon: iconMap[item.icon] || PageIcon, // Default to PageIcon if icon not found
            subItems: item.subItems
              ? item.subItems.map((subItem: any) => ({
                  ...subItem,
                }))
              : undefined,
          })),
        }));
      } else {
        // Fallback to static menu if API fails
        menuGroups.value = getStaticMenu();
      }
    } catch (err) {
      console.error("Error fetching menu:", err);
      error.value = err instanceof Error ? err.message : "Failed to fetch menu";
      // Fallback to static menu on error
      menuGroups.value = getStaticMenu();
    } finally {
      loading.value = false;
      isFetched = true; // Mark as fetched
    }
  };

  const getStaticMenu = () => {
    // Fallback static menu (matches MenuService menu structure with collapse model)
    return [
      {
        title: "Admin",
        items: [
          {
            icon: PageIcon,
            name: "Company",
            subItems: [
              {
                name: "Landing Profile",
                path: "/company/landing-profile",
                pro: false,
              },
              {
                name: "Landing Kegiatan",
                path: "/company/landing-kegiatan",
                pro: false,
              },
              {
                name: "Landing Program",
                path: "/company/landing-program",
                pro: false,
              },
              {
                name: "Landing Proposal",
                path: "/company/landing-proposal",
                pro: false,
              },
              {
                name: "Landing Bulletin",
                path: "/company/landing-bulletin",
                pro: false,
              },
            ],
          },
          {
            icon: FolderIcon,
            name: "Administrasi",
            subItems: [
              {
                name: "Kantor Cabang",
                path: "/administrasi/kantor-cabang",
                pro: false,
              },
              {
                name: "Program",
                path: "/administrasi/program",
                pro: false,
              },
              {
                name: "Jabatan",
                path: "/administrasi/jabatan",
                pro: false,
              },
              {
                name: "Pangkat",
                path: "/administrasi/pangkat",
                pro: false,
              },
              {
                name: "Tipe Absensi",
                path: "/administrasi/tipe-absensi",
                pro: false,
              },
              {
                name: "Gaji",
                path: "/administrasi/gaji",
                pro: false,
              },
              {
                name: "Tipe Donatur",
                path: "/administrasi/tipe-donatur",
                pro: false,
              },
              {
                name: "Form Pesan",
                path: "/administrasi/form-pesan",
                pro: false,
              },
              {
                name: "Kelembagaan",
                path: "/administrasi/kelembagaan",
                pro: false,
              },
              {
                name: "SOP",
                path: "/administrasi/sop",
                pro: false,
              },
              {
                name: "Aturan Kepegawaian",
                path: "/administrasi/aturan-kepegawaian",
                pro: false,
              },
            ],
          },
          {
            icon: ListIcon,
            name: "Konten & Publikasi",
            subItems: [
              {
                name: "Program Kami",
                path: "/konten/program-kami",
                pro: false,
              },
              {
                name: "Profile Kami",
                path: "/konten/profile-kami",
                pro: false,
              },
              {
                name: "Proposal Data",
                path: "/konten/proposal-data",
                pro: false,
              },
              {
                name: "Bulletin Data",
                path: "/konten/bulletin-data",
                pro: false,
              },
            ],
          },
          {
            icon: UserCircleIcon,
            name: "User & Kepegawaian",
            subItems: [
              {
                name: "User",
                path: "/user-kepegawaian/user",
                pro: false,
              },
              {
                name: "Karyawan",
                path: "/user-kepegawaian/karyawan",
                pro: false,
              },
              {
                name: "Mitra",
                path: "/user-kepegawaian/mitra",
                pro: false,
              },
              {
                name: "Donatur",
                path: "/user-kepegawaian/donatur",
                pro: false,
              },
            ],
          },
          {
            icon: CalenderIcon,
            name: "Operasional",
            subItems: [
              {
                name: "Absensi",
                path: "/operasional/absensi",
                pro: false,
              },
              {
                name: "Payroll",
                path: "/operasional/payroll",
                pro: false,
              },
              {
                name: "Payroll Mitra",
                path: "/user-kepegawaian/payroll-mitra",
                pro: false,
              },
            ],
          },
          {
            icon: PieChartIcon,
            name: "Keuangan",
            subItems: [
              {
                name: "Finansial",
                path: "/keuangan/finansial",
                pro: false,
              },
              {
                name: "Transaksi",
                path: "/keuangan/transaksi",
                pro: false,
              },
              {
                name: "Penyaluran",
                path: "/keuangan/penyaluran",
                pro: false,
              },
              {
                name: "Pengajuan Dana",
                path: "/keuangan/pengajuan-dana",
                pro: false,
              },
              {
                name: "Keuangan",
                path: "/keuangan/keuangan",
                pro: false,
              },
            ],
          },
          {
            icon: BarChartIcon,
            name: "Laporan",
            subItems: [
              {
                name: "Laporan Transaksi",
                path: "/laporan/laporan-transaksi",
                pro: false,
              },
              {
                name: "Laporan Keuangan",
                path: "/laporan/laporan-keuangan",
                pro: false,
              },
            ],
          },
        ],
      },
    ];
  };

  // Fetch menu immediately when composable is called for the first time
  if (!isFetched) {
    fetchMenu();
  }

  return {
    menuGroups,
    loading,
    error,
    fetchMenu,
  };
}

