export interface GuestMenuItem {
  label: string;
  routeName: string;
  roles?: string[];
}

export const guestMenu: GuestMenuItem[] = [
  {
    label: "Beranda",
    routeName: "guest-home",
  },
  {
    label: "Courses",
    routeName: "guest-courses",
  },
  {
    label: "Verifikasi Sertifikat",
    routeName: "guest-certificates",
  },
  {
    label: "Tentang Kami",
    routeName: "guest-about",
  },
  {
    label: "Hubungi Kami  ",
    routeName: "guest-contact",
  },
];
