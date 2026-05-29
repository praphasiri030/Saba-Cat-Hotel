# Saba Cat Hotel - Web Application for Cat Hotel Reservations

### เว็บแอปพลิเคชันจองโรงแรมแมว (Saba Cat Hotel)

ปริญญานิพนธ์หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์  
ภาควิชาวิทยาการคอมพิวเตอร์และสารสนเทศ คณะวิทยาศาสตร์และวิศวกรรมศาสตร์  
มหาวิทยาลัยเกษตรศาสตร์ วิทยาเขตเฉลิมพระเกียรติ จังหวัดสกลนคร (พ.ศ. 2567)

---

## Project Overview

ระบบ **Saba Cat Hotel** เป็นเว็บแอปพลิเคชันสำหรับบริหารจัดการการจองโรงแรมแมวแบบครบวงจร ที่ช่วยให้เจ้าของแมวสามารถค้นหาห้องพักที่ว่าง จองห้องพัก และชำระเงินออนไลน์ได้สะดวกมากยิ่งขึ้น พร้อมระบบหลังบ้านสำหรับผู้ดูแลที่ช่วยจัดการข้อมูลการจอง การตรวจสอบการชำระเงิน และการสรุปรายงานต่าง ๆ ได้อย่างมีประสิทธิภาพ

The **Saba Cat Hotel** system was developed to improve traditional cat hotel management processes that relied on manual records and chat-based communication, which often caused booking conflicts and calculation errors.

This project provides a complete web-based reservation and management system that allows customers to search available rooms, make reservations, upload payment slips, and manage booking information conveniently. The system also includes an admin dashboard for booking management, payment verification, refund processing, and business analytics.

---

## Key Features

-  **Real-time Room Availability Search**  
  ค้นหาห้องว่างแบบเรียลไทม์ตามวันเช็กอินและเช็กเอาท์ พร้อมป้องกันการจองซ้ำ

-  **Google Authentication**  
  รองรับการเข้าสู่ระบบผ่าน Google Login และ Email Authentication

-  **1-Hour Payment Deadline**  
  จำกัดเวลาการชำระเงินมัดจำภายใน 1 ชั่วโมงหลังการจอง หากเกินเวลาระบบจะยกเลิกอัตโนมัติ

-  **Automated Refund Calculation**  
  คำนวณอัตราการคืนเงินอัตโนมัติตามนโยบายของโรงแรม

-  **Admin Dashboard & Analytics**  
  สรุปรายงานรายรับ ยอดจอง และสถิติการยกเลิกในรูปแบบกราฟ

-  **Digital Receipt & Check-in System**  
  รองรับการเช็กอิน-เช็กเอาท์ พร้อมออกใบเสร็จดิจิทัล

---

## User Roles

### 1. General User

- สมัครสมาชิกและตรวจสอบอีเมลซ้ำในระบบ
- ดูรายละเอียดห้องพัก ราคา และสิ่งอำนวยความสะดวก
- ค้นหาห้องว่างตามวันที่ต้องการเข้าพัก
- อ่านรีวิวและคะแนนจากผู้ใช้งานจริง

### 2. Member

- เข้าสู่ระบบผ่าน Email หรือ Google Login
- จองห้องพักและระบุรายละเอียดการเข้าพัก
- อัปโหลดสลิปโอนเงินมัดจำ 50%
- ตรวจสอบสถานะการจองย้อนหลัง
- ยกเลิกการจองและกรอกข้อมูลบัญชีธนาคารเพื่อรับเงินคืน
- รีวิวและให้คะแนนหลังการเข้าพัก

### 3. Admin

- จัดการข้อมูลการจองและตรวจสอบการชำระเงิน
- อนุมัติหรือปฏิเสธการจอง
- จัดการคำร้องขอคืนเงินและอัปโหลดหลักฐานการคืนเงิน
- เพิ่ม แก้ไข และลบข้อมูลห้องพัก
- จัดการระบบเช็กอิน-เช็กเอาท์และออกใบเสร็จดิจิทัล
- ดูสรุปรายงานและสถิติการใช้งานระบบ

---

## Room Packages

| Room Type     | Room Size          | Maximum Cats | Price / Day |
| ------------- | ------------------ | ------------ | ----------- |
| Standard Room | 160 × 125 × 70 cm  | 1 Cat        | 300 THB     |
| Superior Room | 108 × 150 × 180 cm | 2 Cats       | 400 THB     |
| Deluxe Room   | 185 × 112 × 220 cm | 4 Cats       | 600 THB     |

---

##  Refund Policy

| Cancellation Period               | Refund Rate |
| --------------------------------- | ----------- |
| More than 15 days before check-in | 100% Refund |
| 7 - 14 days before check-in       | 50% Refund  |
| Less than 7 days before check-in  | No Refund   |

---

## Tech Stack

### Front-End

- HTML5
- CSS3
- JavaScript

### Back-End

- PHP 8

### Database

- MySQL
- phpMyAdmin

### Design & Modeling

- Figma (UI/UX Design)
- ER Diagram (Crow’s Foot Notation)

---

##  Database Schema (14 Tables)

| Table Name      | Description                       |
| --------------- | --------------------------------- |
| `member`        | Member account information        |
| `admin`         | Administrator information         |
| `typ_room`      | Room type details and pricing     |
| `room`          | Room information and availability |
| `room_image`    | Room image gallery                |
| `booking`       | Reservation details               |
| `booking_state` | Booking status                    |
| `cancel`        | Cancellation request information  |
| `cancel_state`  | Refund processing status          |
| `payment`       | Payment and deposit information   |
| `payment_state` | Payment verification status       |
| `review`        | Customer reviews and ratings      |
| `review_typ`    | Review categories                 |
| `receipt`       | Digital receipt information       |

---

##  System Highlights

- ลดปัญหาการจองห้องพักซ้ำ
- เพิ่มความสะดวกในการจองและชำระเงินออนไลน์
- ช่วยให้ผู้ดูแลระบบสามารถบริหารจัดการข้อมูลได้ง่ายขึ้น
- รองรับการวิเคราะห์ข้อมูลและสรุปรายงานธุรกิจ
- ออกแบบระบบให้ใช้งานง่าย รองรับทั้งผู้ใช้งานทั่วไปและผู้ดูแลระบบ

---


