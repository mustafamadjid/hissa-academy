import { Home, BookOpen, FileCheck, Info, Phone } from "@lucide/vue";
import type { Component } from "vue";

export interface GuestMenuItem {
  label: string;
  routeName: string;
  icon: Component;
  roles?: string[];
}

export const guestMenu: GuestMenuItem[] = [
  {
    label: "Beranda",
    routeName: "landing",
    icon: Home,
  },
  {
    label: "Courses",
    routeName: "guest-courses",
    icon: BookOpen,
  },
  {
    label: "Verifikasi Sertifikat",
    routeName: "guest-certificates",
    icon: FileCheck,
  },
  {
    label: "Tentang Kami",
    routeName: "guest-about",
    icon: Info,
  },
  {
    label: "Hubungi Kami  ",
    routeName: "guest-contact",
    icon: Phone,
  },
];
